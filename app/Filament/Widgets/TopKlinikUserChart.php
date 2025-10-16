<?php

namespace App\Filament\Widgets;

use App\Models\ReservasiKlinik;
use Filament\Widgets\ChartWidget;

class TopKlinikUserChart extends ChartWidget
{
    protected static ?string $heading = 'Top 3 Pelanggan Klinik Teraktif (Selesai)';
    protected static ?int $sort = 6;

    protected function getData(): array
    {
        // Ambil data top 3 user berdasarkan total reservasi 'Selesai'
        $topUsers = ReservasiKlinik::where('status', 'Selesai')
            ->with(['hewan.user']) // Eager load sampai ke user
            ->get()
            ->groupBy(function ($reservasi) {
                return $reservasi->hewan?->user?->id;
            })
            ->map(function ($group) {
                return [
                    'user' => $group->first()->hewan?->user,
                    'total' => $group->count(),
                ];
            })
            ->filter(fn($item) => $item['user'] !== null)
            ->sortByDesc('total')
            ->take(3);

        // Siapkan label dan data
        $labels = $topUsers->pluck('user.name')->toArray();
        $data = $topUsers->pluck('total')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Reservasi Selesai',
                    'data' => $data,
                    'backgroundColor' => ['#4CAF50', '#2196F3', '#FFC107'],
                    'borderColor' => ['#388E3C', '#1976D2', '#FFA000'],
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole(['docter', 'clinic_employee']);
    }
}
