<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\User;
use Carbon\Carbon;

class UserChart extends ChartWidget
{
    protected static ?string $heading = 'Jumlah User Baru Per Bulan';

    protected function getData(): array
    {
        $totalUsers = User::count();
    
        $usersPerMonth = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
    
        $labels = collect(range(0, 11))->map(fn ($i) => Carbon::now()->subMonths(11 - $i)->format('Y-m'));
    
        $data = $labels->map(fn ($month) => $usersPerMonth[$month] ?? 0);
    
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah User',
                    'data' => $data->values(),
                    'borderColor' => '#4F46E5',
                    'backgroundColor' => 'rgba(79, 70, 229, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels->map(fn ($month) => Carbon::createFromFormat('Y-m', $month)->translatedFormat('M Y'))->toArray(),
            'totalUsers' => $totalUsers, // Tambahkan total user
        ];
    }
    
    protected function getType(): string
    {
        return 'line'; // Bisa diganti dengan 'bar' jika ingin menggunakan Bar Chart
    }
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }
}
