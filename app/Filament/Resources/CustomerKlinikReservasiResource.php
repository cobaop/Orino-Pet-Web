<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Filament\Resources\CustomerKlinikReservasiResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class CustomerKlinikReservasiResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Pelanggan Klinik Teraktif';

    protected static ?string $navigationGroup = 'Klinik';


    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['docter', 'clinic_employee']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        // return auth()->user()?->hasAnyRole(['super_admin', 'docter', 'clinic_employee']);
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return User::role('customer') // hanya ambil user dengan role 'customer'
            ->withCount(['reservasiKlinik as selesai_count' => function ($query) {
                $query->where('status', 'Selesai');
            }])
            ->orderByDesc('selesai_count');
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pelanggan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),

                Tables\Columns\TextColumn::make('selesai_count')
                    ->label('Total Reservasi Selesai')
                    ->sortable(),
            ])
            ->defaultSort('selesai_count', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerKlinikReservasi::route('/'),
        ];
    }
}
