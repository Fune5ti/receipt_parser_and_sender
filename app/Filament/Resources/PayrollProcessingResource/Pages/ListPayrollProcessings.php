<?php

namespace App\Filament\Resources\PayrollProcessingResource\Pages;

use App\Filament\Resources\PayrollProcessingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayrollProcessings extends ListRecords
{
    protected static string $resource = PayrollProcessingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
