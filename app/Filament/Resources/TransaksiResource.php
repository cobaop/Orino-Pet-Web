<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiResource\Pages;
use App\Models\Transaksi;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Transaksi';

    protected static ?string $navigationGroup = 'Pet Shop';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['manager']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['manager']);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_id')
                    ->label('Order ID')
                    ->sortable(),

                TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal Transaksi')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('d F Y')),
                
                TextColumn::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'Lunas' => 'success',
                        'Menunggu' => 'warning',
                        'gagal' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total Pembayaran')
                    ->sortable()
                    ->money('IDR'),

                TextColumn::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->sortable(),

                TextColumn::make('jenis_layanan')  
                    ->label('Jenis Layanan')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_pembayaran')
                    ->label('Status Pembayaran')
                    ->options([
                        'lunas' => 'Lunas',
                        'menunggu' => 'Menunggu',
                        'gagal' => 'Gagal',
                    ]),
                Tables\Filters\SelectFilter::make('jenis_layanan')
                    ->label('Jenis Layanan')
                    ->options([
                        'grooming' => 'Grooming',
                        'penitipan' => 'Penitipan',
                    ])
                    ->placeholder('Semua Jenis Layanan')
                    ->query(function ($query, $data) {
                        if ($data['value'] === 'grooming') {
                            return $query->whereHas('grooming');
                        } elseif ($data['value'] === 'penitipan') {
                            return $query->whereHas('penitipan');
                        }
                        return $query;
                    }),
            ])
            ->defaultSort('id_transaksi', 'desc')
            ->actions([]);
    }


    public static function getRelations(): array
    {
        return [];
    }

    public static function getModelLabel(): string
    {
        return 'Transaksi';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Transaksi';
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksis::route('/'),
        ];
    }
}
