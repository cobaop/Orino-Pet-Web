<?php

namespace App\Filament\Resources\DataHewanResource\Pages;

use App\Filament\Resources\DataHewanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Barryvdh\DomPDF\Facade\Pdf; // pastikan sudah import facade PDF
use Illuminate\Support\Facades\View;

class ViewDataHewan extends ViewRecord
{
    protected static string $resource = DataHewanResource::class;

    protected static ?string $title = 'Lihat Data Hewan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Back')
                ->label('Kembali ke List')
                ->url($this->getResource()::getUrl('index'))
                ->color('secondary')
                ->icon('heroicon-m-arrow-left'),

            Actions\Action::make('Download')
                ->label('Download Rekam Medis')
                ->icon('heroicon-m-arrow-down-tray')
                ->action(function () {
                    $dataHewan = $this->record->load('riwayatKesehatan');
                    $pdf = Pdf::loadView('exports.riwayat-kesehatan', [
                        'dataHewan' => $dataHewan
                    ]);
                    return response()->streamDownload(
                        fn () => print($pdf->stream()),
                        'rekam-medis-' . $dataHewan->nama_hewan . '.pdf'
                    );
                }),

        //     Actions\EditAction::make()
        //         ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'docter'])),

        //     Actions\DeleteAction::make()
        //         ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'docter'])),
        ];
    }

}
