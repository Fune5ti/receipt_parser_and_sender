<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Collection;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Employees';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('contrib_number')
                    ->label('Contribution Number')
                    ->required()
                    ->maxLength(9)
                    ->unique(ignoreRecord: true)
                    ->disabled(fn($record) => $record !== null),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contrib_number')
                    ->label('Contribution Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('viewReceipts')
                    ->label('View Receipts')
                    ->icon('heroicon-o-document-text')
                    ->modalContent(function (Employee $record) {
                        $receipts = collect();

                        // Get all year directories
                        $years = Storage::disk('public')->directories('receipts');

                        foreach ($years as $yearPath) {
                            $year = basename($yearPath);
                            // Get all month directories for this year
                            $months = Storage::disk('public')->directories($yearPath);

                            foreach ($months as $monthPath) {
                                $month = basename($monthPath);
                                // Check if receipt exists for this employee
                                $receiptPath = "{$monthPath}/{$record->contrib_number}.pdf";

                                if (Storage::disk('public')->exists($receiptPath)) {
                                    $receipts->push([
                                        'year' => $year,
                                        'month' => $month,
                                        'path' => $receiptPath,
                                        'url' => Storage::disk('public')->url($receiptPath),
                                    ]);
                                }
                            }
                        }

                        $receipts = $receipts->sortByDesc('year')->sortByDesc('month');

                        return view('filament.employee.receipts-modal', [
                            'receipts' => $receipts
                        ]);
                    })
                    ->modalWidth('lg')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['contrib_number', 'name', 'email'];
    }
}
