<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenitipanKucingResource\Pages;
use App\Models\PenitipanKucing;
use App\Models\DataKucing;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PenitipanKucingResource extends Resource
{
    protected static ?string $model = PenitipanKucing::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Reservasi Penitipan';
    protected static ?string $navigationGroup = 'Pet Shop';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['manager', 'petshop_employee']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['manager', 'petshop_employee']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('d F Y')),

                TextColumn::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('d F Y')),

                TextColumn::make('kucing.nama_kucing')
                    ->label('Nama Kucing'),

                TextColumn::make('kucing.user.name')
                    ->label('Nama Pemilik')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('kandang.kode_kandang')
                    ->label('Kandang')
                    ->sortable(),
            ])
            ->defaultSort('tanggal_masuk', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat'),
                // EditAction DIHAPUS sepenuhnya
            ])
            ->bulkActions([])
            ->searchPlaceholder('Cari');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getModelLabel(): string
    {
        return 'Reservasi Penitipan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Reservasi Penitipan';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenitipanKucings::route('/'),
            'view' => Pages\ViewPenitipanKucing::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['kucing.user.roles', 'kandang', 'transaksi'])
            ->whereHas('transaksi', function ($query) {
                $query->where('status_pembayaran', 'Lunas');
            })
            ->whereHas('kucing.user.roles', function ($query) {
                $query->where('name', 'customer');
            });
    }

    public static function getNavigationBadge(): ?string
    {
        $jumlahBaru = static::getEloquentQuery()
            ->where('is_read', false)
            ->count();

        return $jumlahBaru > 0 ? (string) $jumlahBaru : null;
    }
}
