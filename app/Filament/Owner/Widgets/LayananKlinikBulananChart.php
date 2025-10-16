<?php

namespace App\Filament\Owner\Widgets;

use App\Models\ReservasiKlinik;
use App\Models\LayananKlinik;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LayananKlinikBulananChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Pemesanan Layanan Klinik per Bulan';

    protected function getData(): array
    {
        // Ambil semua layanan klinik
        $layanans = LayananKlinik::all();

        // Ambil semua bulan yang pernah ada reservasi
        $bulanList = ReservasiKlinik::select(
                DB::raw("DATE_FORMAT(tanggal_reservasi, '%Y-%m') as bulan")
            )
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('bulan')
            ->toArray();

        $warnaList = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#8DD17E', '#D65F5F', '#FFA07A', '#7FB3D5',
        ];

        $datasets = [];
        $index = 0;

        foreach ($layanans as $layanan) {
            $dataPerBulan = [];

            foreach ($bulanList as $bulan) {
                $jumlah = ReservasiKlinik::where('id_layanan', $layanan->id_layanan)
                    ->where(DB::raw("DATE_FORMAT(tanggal_reservasi, '%Y-%m')"), $bulan)
                    ->count();

                $dataPerBulan[] = $jumlah;
            }

            $color = $warnaList[$index % count($warnaList)]; // Loop warna agar tidak kehabisan

            $datasets[] = [
                'label' => $layanan->nama_layanan,
                'data' => $dataPerBulan,
                'borderColor' => $color,
                'backgroundColor' => $color,
                'fill' => false, // Kalau mau area bawahnya diisi warna, bisa set true
                'tension' => 0.3, // Smooth line
            ];

            $index++;
        }

        return [
            'labels' => $bulanList,
            'datasets' => $datasets,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Bisa ganti 'bar' kalau mau
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Jumlah Reservasi Layanan Klinik per Bulan',
                ],
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }

    // ini ga ngaruh cuma yaudahlah, jadi yang di blade yang ngaruh (semuanya)
    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['docter', 'clinic_employee']);
    }
}
