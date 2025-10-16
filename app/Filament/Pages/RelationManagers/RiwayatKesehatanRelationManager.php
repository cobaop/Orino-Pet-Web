<?php

namespace App\Filament\Resources\DataHewanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

class RiwayatKesehatanRelationManager extends RelationManager
{
    protected static string $relationship = 'riwayatKesehatan';
    protected static ?string $title = 'Riwayat Kesehatan';
    protected static ?string $recordTitleAttribute = 'tanggal';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('tanggal')->required(),
            Forms\Components\Textarea::make('pemeriksaan')->required(),
            Forms\Components\Textarea::make('diagnosa')->required(),
            Forms\Components\Textarea::make('terapi')->required(),
        ]);
    }
    

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')->date()->sortable(),
                Tables\Columns\TextColumn::make('pemeriksaan')->limit(30),
                Tables\Columns\TextColumn::make('diagnosa')->limit(30),
                Tables\Columns\TextColumn::make('terapi')->limit(30),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->label('Tambah Riwayat Kesehatan'),
                
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
