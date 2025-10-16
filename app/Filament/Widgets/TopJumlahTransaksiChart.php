<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class TopJumlahTransaksiChart extends ChartWidget
{
    protected static ?string $heading = 'Top 3 User Berdasarkan Jumlah Transaksi (Lunas)';

    protected function getData(): array
    {
        // Ambil user dengan jumlah transaksi lunas terbanyak
        $topUsers = User::withCount(['transaksi as transaksi_lunas_count' => function ($query) {
                $query->where('status_pembayaran', 'Lunas');
            }])
            ->orderByDesc('transaksi_lunas_count')
            ->take(3)
            ->get();

        $labels = $topUsers->pluck('name')->toArray();
        $data = $topUsers->pluck('transaksi_lunas_count')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi (Lunas)',
                    'data' => $data,
                    'backgroundColor' => ['#FF9F40', '#9966FF', '#FF6384'],
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
        return auth()->user()?->hasRole(' manager', 'petshop_employee');
    }
}
