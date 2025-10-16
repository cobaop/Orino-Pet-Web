<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LayananKlinikResource\Pages;
use App\Models\LayananKlinik;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LayananKlinikResource extends Resource
{
    protected static ?string $model = LayananKlinik::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'Layanan Klinik';

    protected static ?string $navigationGroup = 'Klinik';

    // protected static ?int $navigationSort = 4;


    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['docter', 'clinic_employee']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_layanan')
                ->label('Nama Layanan')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('deskripsi_layanan')
                ->label('Deskripsi')
                ->required()
                ->rows(10) 
                ->maxLength(1000),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_layanan')
                    ->label('Nama Layanan')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('deskripsi_layanan')
                    ->label('Deskripsi')
                    ->limit(20)  
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyRole(['docter'])),
                // Tables\Actions\DeleteAction::make()
                //     ->visible(fn () => auth()->user()?->hasRole('super_admin')),
            ])
            ->searchPlaceholder('Cari');
            // ->bulkActions([
            //     Tables\Actions\DeleteBulkAction::make()
            //         ->visible(fn () => auth()->user()?->hasRole('super_admin')),
            // ])

    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getModelLabel(): string
    {
        return 'Layanan Klinik';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Layanan Klinik';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLayananKliniks::route('/'),
            'create' => Pages\CreateLayananKlinik::route('/create'),
            'edit' => Pages\EditLayananKlinik::route('/{record}/edit'),
        ];
    }
}
