<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\Pages\CustomerTransactions;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Transaksi Customer';
    protected static ?string $pluralModelLabel = 'customer';

    protected static ?string $navigationGroup = 'Pet Shop';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole([ 'manager', 'petshop_employee']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        // return auth()->user()?->hasAnyRole(['super_admin', 'manager', 'petshop_employee']);
        return false;
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                User::role('customer')
                    ->withCount([
                        'transaksi as transaksi_count' => function (Builder $query) {
                            $query->where('status_pembayaran', 'lunas');
                        },
                    ])
                    ->withSum([
                        'transaksi as total_transaksi' => function (Builder $query) {
                            $query->where('status_pembayaran', 'lunas');
                        },
                    ], 'total')
                    ->orderByDesc('transaksi_count')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaksi_count')
                    ->label('Jumlah Transaksi (Lunas)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_transaksi')
                    ->label('Total Transaksi (Lunas)')
                    ->money('IDR')
                    ->sortable(),
            ])
                   ->filters([
                SelectFilter::make('urutan')
                    ->label('Urutkan Berdasarkan')
                    ->options([
                        'jumlah' => 'Jumlah Transaksi Terbanyak',
                        'nominal' => 'Nominal Transaksi Terbanyak',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'jumlah') {
                            return $query->orderByDesc('transaksi_count');
                        } elseif ($data['value'] === 'nominal') {
                            return $query->orderByDesc('total_transaksi');
                        }

                        return $query;
                    }),
            ]);
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([]);
    }

    public static function getModelLabel(): string
    {
        return 'Transaksi Pelanggan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Transaksi Pelanggan';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'customer-transactions' => CustomerTransactions::route('/{record}/customer-transactions'), // 👈 Tambah ini
        ];
    }
}