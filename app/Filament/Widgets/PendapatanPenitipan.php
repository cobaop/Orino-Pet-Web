<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Transaksi;
use Carbon\Carbon;

class PendapatanPenitipan extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan Penitipan per Bulan';

    protected function getData(): array
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

        $transaksi = Transaksi::with('penitipan')->where('status_pembayaran', 'Lunas')->get();

        $penitipanData = [];
        foreach ($dateRange as $bulan) {
            $total = $transaksi->filter(function ($item) use ($bulan) {
                return $item->penitipan && Carbon::parse($item->tanggal_transaksi)->format('Y-m') == $bulan;
            })->sum('total');

            $penitipanData[] = $total;
        }

        $labels = $dateRange->map(fn ($date) => Carbon::parse($date)->translatedFormat('M Y'))->toArray();
        if (count($labels) > 0) {
            $labels[count($labels) - 1] = 'Bulan Ini';
        }

        return [
            'labels' => $labels,
            'datasets' => [
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
                'legend' => ['position' => 'bottom'],
                'title' => [
                    'display' => true,
                    'text' => 'Pendapatan Penitipan',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(' manager, petshop_employee');
    }
}
