<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersetujuanGroomingResource\Pages;
use App\Filament\Resources\PersetujuanGroomingResource\RelationManagers;
use App\Models\PersetujuanGrooming;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PersetujuanGroomingResource extends Resource
{
    protected static ?string $model = PersetujuanGrooming::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Modal Persetujuan Grooming';
    protected static ?string $navigationGroup = 'Pet Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Textarea::make('deskripsi')
                ->label('Isi Persetujuan')
                ->required()
                ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('deskripsi')
                ->label('Isi Persetujuan')
                ->wrap()
                ->limit(100),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['manager', 'petshop_employee']);
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['manager', 'petshop_employee']);
    }
    


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersetujuanGroomings::route('/'),
            'create' => Pages\CreatePersetujuanGrooming::route('/create'),
            'edit' => Pages\EditPersetujuanGrooming::route('/{record}/edit'),
        ];
    }
}
