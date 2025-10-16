<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ReservasiGrooming;
use Carbon\Carbon;

class JumlahGroomingChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Reservasi Grooming Per Bulan (Lunas)';
    protected static string $color = 'success';

    protected function getData(): array
    {
        // Ambil data paling awal & akhir berdasarkan tanggal_reservasi
        $startDate = ReservasiGrooming::min('tanggal_reservasi');
        $endDate = ReservasiGrooming::max('tanggal_reservasi');

        if (!$startDate || !$endDate) {
            return [
                'labels' => [],
                'datasets' => [
                    [
                        'label' => 'Jumlah Grooming (Lunas)',
                        'data' => [],
                        'backgroundColor' => 'rgba(76, 175, 80, 0.5)',
                        'borderColor' => 'rgba(76, 175, 80, 1)',
                        'borderWidth' => 1,
                    ],
                ],
            ];
        }

        $start = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->startOfMonth();

        // Generate range bulan
        $dateRange = collect();
        while ($start <= $end) {
            $dateRange->push($start->format('Y-m'));
            $start->addMonth();
        }

        // Ambil data per bulan yang hanya lunas
        $data = ReservasiGrooming::selectRaw('DATE_FORMAT(tanggal_reservasi, "%Y-%m") as bulan, COUNT(*) as jumlah')
            ->join('transaksi', 'reservasi_grooming.transaksi_id', '=', 'transaksi.id_transaksi')
            ->where('transaksi.status_pembayaran', 'Lunas')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('jumlah', 'bulan')
            ->toArray();

        $jumlahGrooming = [];
        foreach ($dateRange as $date) {
            $jumlahGrooming[] = $data[$date] ?? 0;
        }

        $labels = $dateRange->map(fn ($date) => Carbon::parse($date)->translatedFormat('M Y'))->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Grooming (Lunas)',
                    'data' => $jumlahGrooming,
                    'backgroundColor' => 'rgba(76, 175, 80, 0.5)',
                    'borderColor' => 'rgba(76, 175, 80, 1)',
                    'borderWidth' => 1,
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
                'legend' => [
                    'position' => 'bottom',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Jumlah Reservasi Grooming Per Bulan (Lunas)',
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'x' => ['display' => true],
                'y' => [
                    'display' => true,
                    'ticks' => ['precision' => 0],
                ],
            ],
        ];
    }
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('manager', 'petshop_employee');
    }
}
