<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\PenitipanKucing;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class JumlahPenitipanBulananChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah Penitipan Per Bulan (Lunas)';

    protected function getData(): array
    {
        $dataPenitipan = PenitipanKucing::with('transaksi')
            ->whereHas('transaksi', function ($query) {
                $query->where('status_pembayaran', 'Lunas');
            })
            ->get();

        // Kumpulan semua bulan yang terlibat
        $allMonths = collect();

        // Map data penitipan ke bulan-bulan aktif
        $penitipanPerBulan = [];

        foreach ($dataPenitipan as $penitipan) {
            $start = Carbon::parse($penitipan->tanggal_masuk)->startOfMonth();
            $end = Carbon::parse($penitipan->tanggal_keluar)->startOfMonth();

            while ($start <= $end) {
                $bulanKey = $start->format('Y-m');
                $penitipanPerBulan[$bulanKey] = ($penitipanPerBulan[$bulanKey] ?? 0) + 1;
                $allMonths->push($bulanKey);
                $start->addMonth();
            }
        }

        // Ambil semua bulan dari data
        $allMonths = $allMonths->unique()->sort()->values();

        // Build label dan data chart
        $labels = [];
        $data = [];

        foreach ($allMonths as $bulan) {
            $labels[] = Carbon::parse($bulan)->translatedFormat('M Y');
            $data[] = $penitipanPerBulan[$bulan] ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Penitipan (Lunas)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(153, 102, 255, 0.5)',
                    'borderColor' => 'rgba(153, 102, 255, 1)',
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
                    'text' => 'Jumlah Penitipan Per Bulan (Lunas)',
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
        return auth()->user()?->hasRole('manager', 'petshop_employee');
    }
}
