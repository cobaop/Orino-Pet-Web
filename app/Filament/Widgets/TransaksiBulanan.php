<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Transaksi;
use Carbon\Carbon;

class TransaksiBulanan extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan Bulanan';

    public function getData(): array
    {
        $bulanIni = now()->format('Y-m');

        $startMonth = Transaksi::where('status_pembayaran', 'Lunas')->min('tanggal_transaksi');
        $startMonth = $startMonth ? Carbon::parse($startMonth)->format('Y-m') : $bulanIni;

        $dateRange = collect();
        $currentDate = Carbon::parse($startMonth);
        while ($currentDate->format('Y-m') <= $bulanIni) {
            $dateRange->push($currentDate->format('Y-m'));
            $currentDate->addMonth();
        }

        $data = Transaksi::where('status_pembayaran', 'Lunas')
            ->selectRaw('DATE_FORMAT(tanggal_transaksi, "%Y-%m") as bulan, SUM(total) as pendapatan')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('pendapatan', 'bulan')
            ->toArray();

        $pendapatan = [];
        foreach ($dateRange as $date) {
            $pendapatan[] = $data[$date] ?? 0;
        }

        $labels = $dateRange->map(fn ($date) => Carbon::parse($date)->translatedFormat('M Y'))->toArray();
        if (count($labels) > 0) {
            $labels[count($labels) - 1] = 'Bulan Ini';
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pendapatan Bulanan',
                    'data' => $pendapatan,
                    'backgroundColor' => [
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(153, 102, 255, 0.5)',
                        'rgba(255, 159, 64, 0.5)',
                        'rgba(201, 203, 207, 0.5)',
                    ],
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    public function getType(): string
    {
        return 'line';
    }

    public function getOptions(): array
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
                    'text' => 'Pendapatan Bulanan',
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
        ];
    }
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('manager, petshop_employee');
    }
}
