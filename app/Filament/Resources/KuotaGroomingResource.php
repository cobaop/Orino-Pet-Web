<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KuotaGroomingResource\Pages;
use App\Models\KuotaGrooming;
use App\Models\ReservasiGrooming;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class KuotaGroomingResource extends Resource
{
    protected static ?string $model = KuotaGrooming::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Kuota Grooming';

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
            Forms\Components\DatePicker::make('tanggal_ketersediaan')
                ->label('Tanggal Ketersediaan')
                ->required()
                ->minDate(now()) // ini aman
                ->rules([
                    'after_or_equal:today',
                    Rule::unique('kuota_grooming', 'tanggal_ketersediaan')
                        ->ignore(request()->route('record')), // supaya saat edit tidak error duplikat
                ]),

            Forms\Components\TextInput::make('kuota')
                ->label('Kuota')
                ->required()
                ->numeric(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_ketersediaan')
                    ->label('Tanggal Ketersediaan')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->translatedFormat('d F Y')),

                Tables\Columns\TextColumn::make('kuota')
                    ->label('Kuota')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('sisa_kuota')
                    ->label('Sisa Kuota')
                    ->getStateUsing(function ($record) {
                        $tanggal = Carbon::parse($record->tanggal_ketersediaan)->toDateString();

                        $jumlahReservasi = ReservasiGrooming::whereDate('tanggal_reservasi', $tanggal)
                            ->whereHas('transaksi', function ($query) {
                                $query->where(function ($q) {
                                    $q->where('status_pembayaran', 'Lunas')
                                        ->orWhere(function ($q2) {
                                            $q2->where('status_pembayaran', 'Menunggu')
                                                ->where('token_expired_at', '>', Carbon::now());
                                        });
                                });
                            })
                            ->count();

                        return max($record->kuota - $jumlahReservasi, 0);
                    })
                    ->sortable(),
            ])
            ->defaultSort('tanggal_ketersediaan', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) =>
                        auth()->user()?->hasAnyRole(['manager']) &&
                        !Carbon::parse($record->tanggal_ketersediaan)->isPast()
                    ),
            ])
            ->searchPlaceholder('Cari');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getModelLabel(): string
    {
        return 'Kuota Grooming';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kuota Grooming';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKuotaGroomings::route('/'),
            'create' => Pages\CreateKuotaGrooming::route('/create'),
            'edit' => Pages\EditKuotaGrooming::route('/{record}/edit'),
        ];
    }
}
