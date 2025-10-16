<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KandangResource\Pages;
use App\Models\Kandang;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class KandangResource extends Resource
{
    protected static ?string $model = Kandang::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationLabel = 'Kandang';

    protected static ?string $navigationGroup = 'Pet Shop';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['manager', 'petshop_employee']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole([ 'manager', 'petshop_employee']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('kode_kandang')
                ->label('Kode Kandang')
                ->required()
                ->maxLength(10),

            Forms\Components\TextInput::make('deskripsi')
                ->label('Deskripsi')
                ->maxLength(255),

            Forms\Components\TextInput::make('harga_1_kucing_per_hari')
                ->label('Harga 1 Kucing per Hari')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('harga_2_kucing_per_hari')
                ->label('Harga 2 Kucing per Hari')
                ->numeric()
                ->required(),

            Forms\Components\Toggle::make('is_active')
                ->label('Status Aktif')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_kandang')
                    ->label('Kode Kandang')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('harga_1_kucing_per_hari')
                    ->label('Harga 1 Kucing per Hari')
                    ->sortable()
                    ->numeric()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                Tables\Columns\TextColumn::make('harga_2_kucing_per_hari')
                    ->label('Harga 2 Kucing per Hari')
                    ->sortable()
                    ->numeric()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
            ])
            ->filters([])
            ->actions([
        Tables\Actions\EditAction::make()
            ->visible(fn () => auth()->user()?->hasAnyRole(['manager'])),

        // Tables\Actions\Action::make('matikan')
        //     ->label('Kandang Tidak Aktif')
        //     ->icon('heroicon-o-x-circle')
        //     ->color('warning')
        //     ->requiresConfirmation()
        //     ->action(function ($record) {
        //         $oldAttributes = $record->getOriginal();
        //         $record->update(['is_active' => false]);

        //         activity()
        //             ->causedBy(auth()->user())
        //             ->performedOn($record)
        //             ->withProperties([
        //                 'attributes' => ['is_active' => false],
        //                 'old' => $oldAttributes,
        //             ])
        //             ->log('Menonaktifkan kandang: ' . $record->kode_kandang);
        //     })
        //     ->visible(fn ($record) => auth()->user()?->hasRole('super_admin') && $record->is_active),

        // Tables\Actions\Action::make('aktifkan')
        //     ->label('Aktifkan Kandang')
        //     ->icon('heroicon-o-check-circle')
        //     ->color('success')
        //     ->requiresConfirmation()
        //     ->action(function ($record) {
        //         $oldAttributes = $record->getOriginal();
        //         $record->update(['is_active' => true]);

        //         activity()
        //             ->causedBy(auth()->user())
        //             ->performedOn($record)
        //             ->withProperties([
        //                 'attributes' => ['is_active' => true],
        //                 'old' => $oldAttributes,
        //             ])
        //             ->log('Mengaktifkan kandang: ' . $record->kode_kandang);
        //     })
        //     ->visible(fn ($record) => auth()->user()?->hasRole('super_admin') && !$record->is_active),
        ])
                ->searchPlaceholder('Cari');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getModelLabel(): string
    {
        return 'Kandang';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kandang';
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKandangs::route('/'),
            'create' => Pages\CreateKandang::route('/create'),
            'edit' => Pages\EditKandang::route('/{record}/edit'),
        ];
    }
}
