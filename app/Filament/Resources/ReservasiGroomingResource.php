<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservasiGroomingResource\Pages;
use App\Models\ReservasiGrooming;
use Filament\Forms\Form;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Pages\ListRecords;

class ReservasiGroomingResource extends Resource
{
    protected static ?string $model = ReservasiGrooming::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Reservasi Grooming';
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
            Components\Placeholder::make('nama_kucing')
                ->label('Nama Kucing')
                ->content(fn (?ReservasiGrooming $record) => $record?->kucing?->nama_kucing ?? '-'),

            Components\Placeholder::make('nama_pemilik')
                ->label('Nama Pemilik')
                ->content(fn (?ReservasiGrooming $record) => $record?->kucing?->user?->name ?? '-'),

            Components\Placeholder::make('no_whatsapp')
                ->label('Kontak')
                ->content(fn (?ReservasiGrooming $record) => $record?->kucing?->user?->no_whatsapp ?? '-'),
            
            Components\Placeholder::make('alamat')
                ->label('Alamat')
                ->content(fn (?ReservasiGrooming $record) => $record?->kucing?->user?->alamat ?? '-'),
            


            Components\Placeholder::make('jenis_kelamin ')
                ->label('Jenis Kelamin')
                ->content(fn (?ReservasiGrooming $record) => $record?->kucing?->jenis_kelamin ?? '-'),


            Components\DatePicker::make('tanggal_reservasi')
                ->label('Tanggal Reservasi')
                ->required(),

            Components\TimePicker::make('jam_datang')
                ->label('Jam Kedatangan')
                ->required(),

            Components\Textarea::make('catatan')
                ->label('Catatan Tambahan')
                ->nullable()
                ->disabled(),

            Components\Placeholder::make('paket')
                ->label('Paket Grooming')
                ->content(fn (?ReservasiGrooming $record) => $record?->paket?->nama_paket ?? '-'),

            Components\Placeholder::make('opsi_antar_jemput')
                ->label('Opsi Antar Jemput')
                ->content(fn (?ReservasiGrooming $record) => $record?->opsi_antar_jemput == 'iya' ? 'Iya' : 'Tidak'),

            Components\Placeholder::make('alamat')
                ->label('Alamat Penjemputan')
                ->content(fn (?ReservasiGrooming $record) => $record?->alamat ?? '-'),

            // Components\Placeholder::make('latitude')
            //     ->label('Latitude')
            //     ->content(fn (?ReservasiGrooming $record) => $record?->latitude ?? '-'),

            // Components\Placeholder::make('longitude')
            //     ->label('Longitude')
            //     ->content(fn (?ReservasiGrooming $record) => $record?->longitude ?? '-'),
            
            Components\View::make('components.lokasi-maps')
                ->label('Tautan Google Maps')
                ->columnSpanFull(),


            Components\Placeholder::make('kode_transaksi')
                ->label('Kode Transaksi')
                ->content(fn (?ReservasiGrooming $record) => $record?->transaksi?->id_transaksi ?? '-'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_reservasi')
                    ->label('Tanggal Reservasi')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('d F Y')),

                TextColumn::make('kucing.nama_kucing')
                    ->label('Nama Kucing')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('kucing.user.name')
                    ->label('Nama Pemilik')
                    ->sortable()
                    ->searchable(),

                // TextColumn::make('kucing.user.no_whatsapp')
                //     ->label('Kontak')
                //     ->sortable(),

                TextColumn::make('paket.nama_paket')
                    ->label('Paket Grooming')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('opsi_antar_jemput')
                    ->label('Opsi Antar Jemput')
                    ->badge()
                    ->color(fn ($state) => $state === 'iya' ? 'success' : 'danger'),
            ])
            ->defaultSort('tanggal_reservasi', 'desc')
            ->filters([])
             ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat'),
                // Tables\Actions\EditAction::make()
                //     ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'manager'])),
            ])
            ->bulkActions([])
            ->searchPlaceholder('Cari');
    }


    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['kucing.user.roles', 'paket', 'transaksi']) // penting: include eager loading roles
            ->whereHas('transaksi', function ($query) {
                $query->where('status_pembayaran', 'lunas');
            })
            ->whereHas('kucing.user.roles', function ($query) {
                $query->where('name', 'customer');
            })
            ->orderByDesc('tanggal_reservasi') // urut tanggal dari terbaru
            ->orderByDesc('id_reservasi_grooming'); // jika tanggal sama, ambil ID terbaru
        
    }

    public static function getModelLabel(): string
    {
        return 'Reservasi Grooming';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Reservasi Grooming';
    }
    public static function getNavigationBadge(): ?string
    {
        $jumlahBaru = static::getEloquentQuery()
            ->where('is_read', false)
            ->count();

        return $jumlahBaru > 0 ? (string) $jumlahBaru : null;
    }




    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservasiGroomings::route('/'),
            // 'edit' => Pages\EditReservasiGrooming::route('/{record}/edit'),
            'view' => Pages\ViewReservasiGrooming::route('/{record}'),
        ];
    }
}
