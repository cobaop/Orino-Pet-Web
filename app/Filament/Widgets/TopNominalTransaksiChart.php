<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class TopNominalTransaksiChart extends ChartWidget
{
    protected static ?string $heading = 'Top 3 User Berdasarkan Total Nominal Transaksi';

    protected function getData(): array
    {
        // Ambil user dengan total transaksi LUNAS terbesar
        $topUsers = User::withSum(['transaksi' => function ($query) {
            $query->where('status_pembayaran', 'Lunas');
        }], 'total')
        ->orderByDesc('transaksi_sum_total')
        ->take(3)
        ->get();

        $labels = $topUsers->pluck('name')->toArray();
        $data = $topUsers->pluck('transaksi_sum_total')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Nominal (Rp)',
                    'data' => $data,
                    'backgroundColor' => ['#4BC0C0', '#36A2EB', '#FF6384'],
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
            'plugins' => [
                'legend' => ['display' => false],
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
        return auth()->user()?->hasRole(['manager', 'petshop_employee']);
    }
}
