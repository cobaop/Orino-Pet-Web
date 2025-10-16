<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Transaksi;
use Carbon\Carbon;

class PendapatanPerLayanan extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan Grooming vs Penitipan Per Bulan';

    protected function getData(): array
    {
        // Ambil bulan awal dari data transaksi Lunas
        $bulanIni = now()->format('Y-m');
        $startMonth = Transaksi::where('status_pembayaran', 'Lunas')->min('tanggal_transaksi');
        $startMonth = $startMonth ? Carbon::parse($startMonth)->format('Y-m') : $bulanIni;

        // Generate range bulan
        $dateRange = collect();
        $currentDate = Carbon::parse($startMonth);
        while ($currentDate->format('Y-m') <= $bulanIni) {
            $dateRange->push($currentDate->format('Y-m'));
            $currentDate->addMonth();
        }

        // Ambil semua transaksi Lunas
        $transaksi = Transaksi::with(['grooming', 'penitipan'])
            ->where('status_pembayaran', 'Lunas')
            ->get();

        // Inisialisasi array per bulan
        $groomingData = [];
        $penitipanData = [];

        foreach ($dateRange as $bulan) {
            $groomingTotal = $transaksi->filter(function ($item) use ($bulan) {
                return $item->grooming && Carbon::parse($item->tanggal_transaksi)->format('Y-m') == $bulan;
            })->sum('total');

            $penitipanTotal = $transaksi->filter(function ($item) use ($bulan) {
                return $item->penitipan && Carbon::parse($item->tanggal_transaksi)->format('Y-m') == $bulan;
            })->sum('total');

            $groomingData[] = $groomingTotal;
            $penitipanData[] = $penitipanTotal;
        }

        // Label bulan
        $labels = $dateRange->map(fn ($date) => Carbon::parse($date)->translatedFormat('M Y'))->toArray();
        if (count($labels) > 0) {
            $labels[count($labels) - 1] = 'Bulan Ini';
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Grooming',
                    'data' => $groomingData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.6)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Penitipan',
                    'data' => $penitipanData,
                    'backgroundColor' => 'rgba(255, 206, 86, 0.6)',
                    'borderColor' => 'rgba(255, 206, 86, 1)',
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
                    'text' => 'Pendapatan Grooming vs Penitipan',
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
                'datalabels' => [
                    'anchor' => 'end',
                    'align' => 'top',
                    'formatter' => 'function(value) { return value.toLocaleString(); }',
                    'color' => '#000',
                    'font' => [
                        'weight' => 'bold'
                    ]
                ]
            ],
            'scales' => [
                'y' => [
                    'type' => 'logarithmic',
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('manager, petshop_employee');
    }
}
