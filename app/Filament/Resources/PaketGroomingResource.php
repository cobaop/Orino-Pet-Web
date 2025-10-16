<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaketGroomingResource\Pages;
use App\Models\PaketGrooming;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Columns\IconColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;

class PaketGroomingResource extends Resource
{
    protected static ?string $model = PaketGrooming::class;
    protected static ?string $navigationIcon = 'heroicon-o-scissors';
    protected static ?string $navigationLabel = 'Paket Grooming';
    protected static ?string $navigationGroup = 'Pet Shop';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['manager', 'petshop_employee']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['manager', 'petshop_employee']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_paket')
                ->label('Nama Paket')
                ->required()
                ->maxLength(100),

            Forms\Components\TextInput::make('harga')
                ->label('Harga (Rp)')
                ->required()
                ->numeric()
                ->prefix('Rp'),

            Forms\Components\Toggle::make('is_active')
                ->label('Status Aktif')
                ->default(true)
                ->inline(false)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_paket')
                    ->label('Nama Paket')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga (Rp)')
                    ->sortable()
                     ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Paket')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->placeholder('Semua'),
            ])
     ->actions([
            Tables\Actions\EditAction::make()
                ->visible(fn () => auth()->user()?->hasAnyRole(['manager'])),
        ])
                ->searchPlaceholder('Cari');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
    public static function getModelLabel(): string
    {
        return 'Paket Grooming';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Paket Grooming';
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaketGroomings::route('/'),
            'create' => Pages\CreatePaketGrooming::route('/create'),
            'edit' => Pages\EditPaketGrooming::route('/{record}/edit'),
        ];
    }
}
