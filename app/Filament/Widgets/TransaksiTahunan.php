<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Transaksi;
use Carbon\Carbon;

class TransaksiTahunan extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan Tahunan';
    protected int | string | array $columnSpan = 'full';

    public function getData(): array
    {
        $tahunMulai = Transaksi::where('status_pembayaran', 'Lunas')->min('tanggal_transaksi');
        $tahunMulai = $tahunMulai ? Carbon::parse($tahunMulai)->year : now()->year;
        $tahunSekarang = now()->year;

        $tahunRange = range($tahunMulai, $tahunSekarang);

        // Ambil data transaksi lunas per tahun
        $data = Transaksi::where('status_pembayaran', 'Lunas')
            ->selectRaw('YEAR(tanggal_transaksi) as tahun, SUM(total) as pendapatan')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->pluck('pendapatan', 'tahun')
            ->toArray();

        $pendapatan = array_map(fn ($tahun) => $data[$tahun] ?? 0, $tahunRange);

        // Warna dinamis berdasarkan jumlah tahun
        $warnaDasar = [
            'rgba(255, 99, 132, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(255, 206, 86, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(255, 159, 64, 0.5)',
        ];
        $warnaBorder = [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
        ];
        $bgColors = array_slice(array_merge($warnaDasar, $warnaDasar), 0, count($tahunRange));
        $borderColors = array_slice(array_merge($warnaBorder, $warnaBorder), 0, count($tahunRange));

        return [
            'labels' => $tahunRange,
            'datasets' => [
                [
                    'label' => 'Pendapatan Tahunan',
                    'data' => $pendapatan,
                    'backgroundColor' => $bgColors,
                    'borderColor' => $borderColors,
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    public function getType(): string
    {
        return 'bar'; // atau 'bar' jika ingin batang
    }

    public function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['position' => 'top'],
                'tooltip' => ['enabled' => true],
            ],
        ];
    }
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('manager, petshop_employee');
    }
}
