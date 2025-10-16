<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\ActivityResource\Widgets\ActivityOverview;

class ViewActivity extends ViewRecord
{
    protected static string $resource = ActivityResource::class;

    // ✅ Tambahan untuk debug, memastikan mount() dipanggil
    public function mount($record): void
    {
        parent::mount($record);

        \Log::info('✅ mount ViewActivity dipanggil!', [
            'record_id' => $this->record?->id,
        ]);
    }

    // 🧩 Menampilkan schema dari form sebagai placeholder
    protected function getFormSchema(): array
    {
        \Log::info('📋 ViewActivity::getFormSchema dijalankan');

        return [
            Section::make('Informasi Aktivitas')
                ->schema([
                    Placeholder::make('description')
                        ->label('Deskripsi')
                        ->content(fn () => $this->record->description),

                    Placeholder::make('causer')
                        ->label('Dilakukan oleh')
                        ->content(fn () => optional($this->record->causer)->name ?? '-'),

                    Placeholder::make('created_at')
                        ->label('Waktu')
                        ->content(fn () => $this->record->created_at->translatedFormat('d F Y H:i')),
                ]),

            Section::make('Perubahan Data')
                ->schema([
                    Placeholder::make('activity_details')
                        ->label('Detail Aktivitas')
                        ->content(function () {
                            if (! $this->record) {
                                return '<p style="color:red;">Data aktivitas tidak ditemukan.</p>';
                            }

                            return '<div><strong>Deskripsi:</strong> ' . e($this->record->description) . '<br>'
                                . '<strong>Log Name:</strong> ' . e($this->record->log_name) . '<br>'
                                . '<strong>Properties:</strong><pre>' . print_r($this->record->properties?->toArray(), true) . '</pre></div>';
                        })
                        ->html(),
                ]),
        ];
    }

    // ❌ Menonaktifkan tombol edit/delete
    protected function getHeaderActions(): array
    {
        return [];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            ActivityOverview::class,
        ];
    }
}
