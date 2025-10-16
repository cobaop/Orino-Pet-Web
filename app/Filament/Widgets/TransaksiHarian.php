<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Transaksi;
use Carbon\Carbon;

class TransaksiHarian extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan Harian';

    protected $listeners = ['refreshChart' => '$refresh'];

    public function getData(): array
    {
        $hariIni = Carbon::today()->format('Y-m-d');

        // Ambil tanggal awal dari transaksi lunas pertama
        $startDate = Transaksi::where('status_pembayaran', 'Lunas')->min('tanggal_transaksi') ?? $hariIni;
        $endDate = $hariIni;

        // Buat rentang tanggal dari transaksi lunas pertama hingga hari ini
        $dateRange = collect();
        $currentDate = Carbon::parse($startDate);
        while ($currentDate->format('Y-m-d') <= $endDate) {
            $dateRange->push($currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }

        // Ambil data transaksi lunas per tanggal
        $data = Transaksi::where('status_pembayaran', 'Lunas')
            ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total) as pendapatan')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->pluck('pendapatan', 'tanggal')
            ->toArray();

        // Gabungkan data transaksi dengan rentang tanggal (isi 0 jika tidak ada transaksi)
        $pendapatan = [];
        foreach ($dateRange as $date) {
            $pendapatan[] = $data[$date] ?? 0;
        }

        // Ganti label terakhir dengan "Hari ini"
        $labels = $dateRange->map(function ($date) use ($hariIni) {
            return $date === $hariIni ? 'Hari ini' : Carbon::parse($date)->translatedFormat('d M');
        })->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pendapatan Harian',
                    'data' => $pendapatan,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
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
            'scales' => [
                'x' => [
                    'ticks' => [
                        'autoSkip' => true,
                        'maxRotation' => 30,
                        'minRotation' => 30,
                        'stepSize' => 5,
                    ],
                ],
            ],
            'plugins' => [
                'zoom' => [
                    'pan' => [
                        'enabled' => true,
                        'mode' => 'x',
                    ],
                    'zoom' => [
                        'wheel' => [
                            'enabled' => true,
                        ],
                        'pinch' => [
                            'enabled' => true,
                        ],
                        'mode' => 'x',
                    ],
                ],
            ],
        ];
    }
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('manager, petshop_employee');
    }
}
