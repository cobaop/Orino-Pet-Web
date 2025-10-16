<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use Spatie\Activitylog\Models\Activity;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Activity Log';

    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Admin Permission';

    // Hanya super_admin yang bisa melihat daftar aktivitas
    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasRole('super_admin');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')->label('Deskripsi')->searchable(),
                Tables\Columns\TextColumn::make('causer.name')->label('Dilakukan oleh')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Waktu')->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('d F Y'))

            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat'),
            ])
            ->bulkActions([]);
    }


    public static function getRelations(): array
    {
        return [];
    }
    public static function getModelLabel(): string
    {
        return 'Activity Log';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Activity Log';
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'view' => Pages\ViewActivity::route('/{record}'),
        ];
    }
}
