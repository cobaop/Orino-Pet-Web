<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Owner\Widgets\TransaksiTahunan;
use App\Filament\Owner\Widgets\TransaksiBulanan;
use App\Filament\Owner\Widgets\TransaksiHarian;
use App\Filament\Owner\Widgets\LayananKlinikBulananChart;
use App\Models\Transaksi;
use Carbon\Carbon;

class Dashboard extends BaseDashboard
{
    protected static string $view = 'filament.owner.pages.dashboard';
    protected static ?string $title = 'Dashboard';


    public function getViewData(): array
    {
        $today = Carbon::today()->format('Y-m-d');
        $pendapatanHariIni = Transaksi::where('status_pembayaran', 'Lunas')
            ->whereDate('tanggal_transaksi', $today)
            ->sum('total');

        $pendapatanBulanIni = Transaksi::where('status_pembayaran', 'Lunas')
            ->whereMonth('tanggal_transaksi', now()->month)
            ->whereYear('tanggal_transaksi', now()->year)
            ->sum('total');

        $pendapatanTahunan = Transaksi::where('status_pembayaran', 'Lunas')
            ->whereYear('tanggal_transaksi', now()->year)
            ->sum('total');

        return [
            'user' => auth()->user(),
            'pendapatanHariIni' => $pendapatanHariIni ?? 0,
            'pendapatanBulanIni' => $pendapatanBulanIni ?? 0,
            'pendapatanTahunan' => $pendapatanTahunan ?? 0,
        ];
    }
    public function getWidgets(): array
    {
        return [
            TransaksiHarian::class,
            TransaksiBulanan::class,
            TransaksiTahunan::class,
            LayananKlinikBulananChart::class,
        ];
    }
}
