<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GallupReportSheetResource\Pages;
use App\Filament\Resources\GallupReportSheetResource\RelationManagers;
use App\Models\GallupReportSheet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GallupReportSheetResource extends Resource
{
    protected static ?string $model = GallupReportSheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationLabel = 'Gallup отчёты';

    protected static ?string $modelLabel = 'Gallup отчёт';

    protected static ?string $pluralModelLabel = 'Gallup отчёты';

    protected static ?string $navigationGroup = 'Настройки';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name_report')
                    ->label('Report Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., DPs, DPT, FMD'),
                Forms\Components\TextInput::make('spreadsheet_id')
                    ->label('Google Spreadsheet ID')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Google Sheets spreadsheet ID'),
                Forms\Components\TextInput::make('gid')
                    ->label('Sheet GID')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Google Sheets GID'),
                Forms\Components\TextInput::make('short_gid')
                    ->label('Sheet Short GID')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Google Sheets Short GID'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_report')
                    ->label('Report Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('spreadsheet_id')
                    ->label('Spreadsheet ID')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('gid')
                    ->label('GID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('indices_count')
                    ->label('Indices Count')
                    ->counts('indices')
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\IndicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGallupReportSheets::route('/'),
            'create' => Pages\CreateGallupReportSheet::route('/create'),
            'edit' => Pages\EditGallupReportSheet::route('/{record}/edit'),
        ];
    }
}
