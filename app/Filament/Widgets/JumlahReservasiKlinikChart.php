<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class JumlahReservasiKlinikChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Reservasi Klinik';
    protected static string $color = 'success';

    protected function getData(): array
    {
        // Mengambil jumlah reservasi berdasarkan status
        $statuses = ['Selesai', 'Proses', 'Dibatalkan'];
        $data = [];

        foreach ($statuses as $status) {
            $data[] = DB::table('reservasi_klinik')
                ->where('status', $status)
                ->count();
        }

        return [
            'labels' => $statuses,
            'datasets' => [
                [
                    'label' => 'Jumlah Reservasi',
                    'data' => $data,
                    'backgroundColor' => ['#4CAF50', '#FFC107', '#F44336'],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar'; 
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
        ];
    }
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('docter', 'clinic_employee');
    }
}
