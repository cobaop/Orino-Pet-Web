<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservasiKlinikResource\Pages;
use App\Models\ReservasiKlinik;
use App\Notifications\ReservasiDibatalkan;
use App\Notifications\ReservasiDivalidasi;
use App\Notifications\ReservasiSelesai;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;
use Filament\Tables\Actions\ViewAction;

class ReservasiKlinikResource extends Resource
{
    protected static ?string $model = ReservasiKlinik::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Reservasi Klinik';
    protected static ?string $navigationGroup = 'Klinik';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['docter', 'clinic_employee']);
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            // ->whereHas('hewan.user.roles', function ($query) {
            //     $query->where('name', 'customer');
            // })
            ->with(['hewan.user'])
            ->orderByDesc('tanggal_reservasi')
            ->orderByDesc('jam_reservasi');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('id_hewan')
                ->label('Hewan')
                ->relationship('hewan', 'nama_hewan')
                ->getOptionLabelUsing(fn ($value) =>
                    optional(\App\Models\DataHewan::with('user')->find($value))
                        ?->nama_hewan . ' - ' . optional(\App\Models\DataHewan::with('user')->find($value))->user?->name
                )
                ->searchable()
                ->preload()
                ->required(),

            DatePicker::make('tanggal_reservasi')
                ->label('Tanggal Reservasi')
                ->required(),

            TimePicker::make('jam_reservasi')
                ->label('Jam Reservasi')
                ->seconds(false)
                ->required(),

            Textarea::make('keluhan')
                ->label('Keluhan')
                ->rows(3),

            Select::make('status')
                ->label('Status')
                ->options([
                    'Menunggu Validasi' => 'Menunggu Validasi',
                    'Proses' => 'Proses',
                    'Selesai' => 'Selesai',
                    'Dibatalkan' => 'Dibatalkan',
                ])
                ->default('Menunggu Validasi')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hewan.nama_hewan')->label('Nama Hewan')->sortable()->searchable(),
                TextColumn::make('layanan.nama_layanan')->label('Nama Layanan')->sortable()->searchable(),
                TextColumn::make('hewan.jenis_hewan')->label('Jenis')->sortable()->searchable(),
                TextColumn::make('tanggal_reservasi')
                    ->label('Tanggal')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('d/m/Y')),
                TextColumn::make('jam_reservasi')->label('Jam')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->sortable()
                    ->color(fn ($state) => match ($state) {
                        'Menunggu Validasi' => 'gray',
                        'Proses' => 'warning',
                        'Selesai' => 'success',
                        'Dibatalkan' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Menunggu Validasi' => 'Menunggu Validasi',
                        'Proses' => 'Proses',
                        'Selesai' => 'Selesai',
                        'Dibatalkan' => 'Dibatalkan',
                    ]),
                Tables\Filters\SelectFilter::make('id_layanan')
                    ->label('Layanan')
                    ->options(fn () => \App\Models\LayananKlinik::pluck('nama_layanan', 'id_layanan')->toArray()),
            ])
            ->actions([
                ViewAction::make()->label('Lihat'),

                Action::make('terima')
                    ->label('✔ Terima')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'Menunggu Validasi' && auth()->user()->hasAnyRole(['docter']))
                    ->action(function (ReservasiKlinik $record) {
                        $old = $record->status;
                        $record->update(['status' => 'Proses']);
                        activity()
                            ->causedBy(auth()->user())
                            ->performedOn($record)
                            ->withProperties([
                                'old' => ['status' => $old],
                                'attributes' => ['status' => $record->status],
                            ])
                            ->log('Reservasi Klinik Divalidasi');
                        $record->hewan->user?->notify(new ReservasiDivalidasi($record));
                        Notification::make()->title('Reservasi Divalidasi')->success()->send();
                    }),

                Action::make('tolak')
                    ->label('✖ Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->modalHeading('Tolak Reservasi')
                    ->modalSubmitActionLabel('Kirim Alasan')
                    ->visible(fn ($record) => $record->status === 'Menunggu Validasi' && auth()->user()->hasAnyRole(['docter']))
                    ->action(function (array $data, ReservasiKlinik $record) {
                        $old = $record->status;
                        $record->update([
                            'status' => 'Dibatalkan',
                            'alasan_penolakan' => $data['alasan_penolakan'],
                        ]);
                        activity()
                            ->causedBy(auth()->user())
                            ->performedOn($record)
                            ->withProperties([
                                'old' => ['status' => $old],
                                'attributes' => ['status' => 'Dibatalkan'],
                            ])
                            ->log('Reservasi Klinik Ditolak');
                        $record->hewan->user?->notify(new ReservasiDibatalkan($record));
                        Notification::make()->title('Reservasi Ditolak')->danger()->send();
                    }),

                Action::make('selesai')
                    ->label('✔ Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'Proses' && auth()->user()->hasAnyRole(['docter']))
                    ->action(function (ReservasiKlinik $record) {
                        $old = $record->status;
                        $record->update(['status' => 'Selesai']);
                        activity()
                            ->causedBy(auth()->user())
                            ->performedOn($record)
                            ->withProperties([
                                'old' => ['status' => $old],
                                'attributes' => ['status' => 'Selesai'],
                            ])
                            ->log('Reservasi Klinik Diselesaikan');
                        $record->hewan->user?->notify(new ReservasiSelesai($record));
                        Notification::make()->title('Reservasi Selesai')->success()->send();
                    }),

                Action::make('batalkan')
                    ->label('✖ Batalkan')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'Proses' && auth()->user()->hasAnyRole(['docter']))
                    ->action(function (ReservasiKlinik $record) {
                        $old = $record->status;
                        $record->update(['status' => 'Dibatalkan']);
                        activity()
                            ->causedBy(auth()->user())
                            ->performedOn($record)
                            ->withProperties([
                                'old' => ['status' => $old],
                                'attributes' => ['status' => 'Dibatalkan'],
                            ])
                            ->log('Reservasi Klinik Dibatalkan');
                        $record->hewan->user?->notify(new ReservasiDibatalkan($record));
                        Notification::make()->title('Reservasi Dibatalkan')->danger()->send();
                    }),
            ])
            ->searchPlaceholder('Cari');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getModelLabel(): string
    {
        return 'Reservasi Klinik';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Reservasi Klinik';
    }

    public static function getNavigationBadge(): ?string
    {
        $jumlahMenunggu = ReservasiKlinik::where('status', 'Menunggu Validasi')->count();
        return $jumlahMenunggu > 0 ? (string) $jumlahMenunggu : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservasiKliniks::route('/'),
            'create' => Pages\CreateReservasiKlinik::route('/create'),
            'view' => Pages\ViewReservasiKlinik::route('/{record}'),
        ];
    }
}
