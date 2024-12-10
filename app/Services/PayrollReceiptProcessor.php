<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Smalot\PdfParser\Parser;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Log;
use Exception;

class PayrollReceiptProcessor
{
    private $month;
    private $year;
    private $parser;
    private $baseStoragePath;
    // Standard A4 dimensions in mm
    private const PAGE_WIDTH = 297; // A4 landscape width
    private const PAGE_HEIGHT = 210; // A4 landscape height
    private const HALF_WIDTH = 148.5; // Half of landscape width
    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
        $this->parser = new Parser();
        $this->baseStoragePath = "receipts/{$this->year}/{$this->month}";

        ini_set('memory_limit', '256M');
    }

    public function process($pdfPath)
    {
        Storage::disk('public')->makeDirectory($this->baseStoragePath);

        $results = [
            'processed' => 0,
            'sent' => 0,
            'errors' => []
        ];

        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($pdfPath);

            for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                $this->processPage($pdfPath, $pageNumber, $results);
            }
        } catch (Exception $e) {
            Log::error("PDF Processing Error: " . $e->getMessage());
            $results['errors'][] = "PDF Processing Error: " . $e->getMessage();
        }

        return $results;
    }

    private function processPage($pdfPath, $pageNumber, &$results)
    {
        $tempPath = $this->createSinglePagePdf($pdfPath, $pageNumber);
        if (!$tempPath) {
            $results['errors'][] = "Page {$pageNumber}: Failed to create single page PDF.";
            return;
        }

        try {
            $text = $this->parser->parseFile($tempPath)->getText();

            $extractedValue = explode(" ", $this->extractFieldValue($text, "N.º Contrib"));

            $number = collect($extractedValue)
                ->last(function ($item) {
                    return preg_match('/\d{9}/', $item);
                });

            if ($number) {
                preg_match('/(\d{9})/', $number, $matches);
                $contribNumber = $matches[1] ?? null;
            }
            Log::info($contribNumber);

            if (!$contribNumber) {
                throw new Exception("No contributor number found.");
            }

            $employee = Employee::where('contrib_number', $contribNumber)->first();

            if (!$employee) {
                throw new Exception("No employee found for contributor number {$contribNumber}.");
            }

            $this->processReceipt($tempPath, $contribNumber, $employee);
            $results['processed']++;
            if ($employee->email) {
                $results['sent']++;
            }
        } catch (Exception $e) {
            Log::error("Page {$pageNumber}: " . $e->getMessage());
            $results['errors'][] = "Page {$pageNumber}: " . $e->getMessage();
        } finally {
            @unlink($tempPath);
        }
    }


    private function extractFieldValue($text, $fieldLabel)
    {
        // Split the text into lines for processing
        $lines = explode("\n", $text);

        // Find the line containing the field label
        $labelLineIndex = null;
        foreach ($lines as $index => $line) {
            if (str_contains(trim($line), $fieldLabel)) {
                $labelLineIndex = $index;
                break;
            }
        }

        if ($labelLineIndex === null) {
            // Field label not found
            return null;
        }

        // Extract the text after the field label in the same line
        $labelLine = $lines[$labelLineIndex];
        $labelPosition = strpos($labelLine, $fieldLabel);
        $value = trim(substr($labelLine, $labelPosition + strlen($fieldLabel)));

        if (!empty($value)) {
            return $value;
        }

        // If the value is not on the same line, check subsequent lines for potential values
        for ($i = $labelLineIndex + 1; $i < count($lines); $i++) {
            $possibleValue = trim($lines[$i]);
            if (!empty($possibleValue)) {
                return $possibleValue;
            }
        }

        // No value found
        return null;
    }

    private function createSinglePagePdf($pdfPath, $pageNumber)
    {
        $tempPdf = new Fpdi();
        // Set page format to original landscape A4
        $tempPdf->AddPage('L','A4');
        $tempPdf->setSourceFile($pdfPath);
        $templateId = $tempPdf->importPage($pageNumber);

        // Get the template size to check orientation
        $templateSize = $tempPdf->getTemplateSize($templateId);

        // Use the template without any scaling or positioning
        $tempPdf->useTemplate($templateId);

        $tempPath = storage_path('app/temp_' . uniqid() . '.pdf');
        $tempPdf->Output('F', $tempPath);

        return $tempPath;
    }

    private function extractContribNumber($text)
    {
        $pattern = '/N.º Contrib\.\s+(\d+)/';
        if (preg_match($pattern, $text, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function processReceipt($tempPath, $contribNumber, Employee $employee)
    {
        $outputPath = "{$this->baseStoragePath}/{$contribNumber}.pdf";
        $this->createHalfPagePdf($tempPath, $outputPath);

        if ($employee->email) {
            $this->sendReceiptEmail($employee, $outputPath);
        }

        Log::info("Processed receipt for contributor {$contribNumber}");
    }

    private function createHalfPagePdf($sourcePdf, $outputPath)
    {
        $pdf = new Fpdi();
        // Create a new PDF with A5 landscape dimensions (half of A4 landscape)
        $pdf->AddPage('P', 'A5');
        $pdf->setSourceFile($sourcePdf);
        $templateId = $pdf->importPage(1);

        // Get original template size
        $templateSize = $pdf->getTemplateSize($templateId);

        // Calculate the scale factor to fit the half width while maintaining aspect ratio
        // $scale = self::HALF_WIDTH / $templateSize['width'];

        // Use template with negative x-offset to show only the left half
        // Multiply width by 2 to maintain aspect ratio since we're only showing half
        $pdf->useTemplate($templateId, 0, 0, $templateSize['width'], $templateSize['height'] );

        Storage::disk('public')->put($outputPath, $pdf->Output('S'));
    }

    private function sendReceiptEmail(Employee $employee, $filePath)
    {
        Mail::send('emails.payroll-receipt', [
            'employee' => $employee,
            'month' => $this->month,
            'year' => $this->year
        ], function ($message) use ($employee, $filePath) {
            $message->to($employee->email)
                ->subject("Payroll Receipt - {$this->month}/{$this->year}")
                ->attach(Storage::disk('public')->path($filePath));
        });
    }
}
