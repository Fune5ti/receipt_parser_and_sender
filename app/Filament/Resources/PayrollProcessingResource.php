<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollProcessingResource\Pages;
use App\Models\PayrollProcessing;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Services\PayrollReceiptProcessor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PayrollProcessingResource extends Resource
{
    protected static ?string $model = PayrollProcessing::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Payroll Processing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('month')
                    ->options([
                        1 => 'January',
                        2 => 'February',
                        3 => 'March',
                        4 => 'April',
                        5 => 'May',
                        6 => 'June',
                        7 => 'July',
                        8 => 'August',
                        9 => 'September',
                        10 => 'October',
                        11 => 'November',
                        12 => 'December',
                    ])
                    ->required(),

                Forms\Components\Select::make('year')
                    ->options(array_combine(
                        range(date('Y') - 1, date('Y') + 1),
                        range(date('Y') - 1, date('Y') + 1)
                    ))
                    ->required(),

                Forms\Components\FileUpload::make('pdf_path')
                    ->label('Payroll PDF')
                    ->directory('temp-payroll')
                    ->acceptedFileTypes(['application/pdf'])
                    ->required()
                    ->preserveFilenames(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->formatStateUsing(fn($state) => Carbon::create(2000, $state, 1)->format('F')),
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('total_processed')
                    ->label('Processed'),
                Tables\Columns\TextColumn::make('total_sent')
                    ->label('Sent'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('processed_at')
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\Action::make('process')
                    ->action(function (PayrollProcessing $record) {
                        try {
                            $record->update(['status' => 'processing']);
                            $processor = new PayrollReceiptProcessor(
                                $record->month,
                                $record->year
                            );

                            $result = $processor->process(
                                Storage::disk('public')->path($record->pdf_path)
                            );

                            $record->update([
                                'status' => 'completed',
                                'processed_at' => now(),
                                'total_processed' => $result['processed'],
                                'total_sent' => $result['sent']
                            ]);

                            // Clean up temp file
                            Storage::disk('public')->delete($record->pdf_path);
                        } catch (\Exception $e) {
                            $record->update(['status' => 'failed']);
                            throw $e;
                        }
                    })
                    ->visible(fn(PayrollProcessing $record) => $record->status === 'pending')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrollProcessings::route('/'),
            'create' => Pages\CreatePayrollProcessing::route('/create'),
        ];
    }
}
