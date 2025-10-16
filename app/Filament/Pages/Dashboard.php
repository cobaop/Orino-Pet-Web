<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Transaksi;
use App\Models\User;
use App\Filament\Widgets\PendapatanGrooming;
use App\Filament\Widgets\PendapatanPenitipan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public Collection $customers;

    public function mount(): void
    {
        $this->customers = User::role('customer')
        ->whereHas('transaksi', function ($query) {
            $query->where('status_pembayaran', 'lunas');
        })
        ->withCount([
            'transaksi as jumlah_transaksi_lunas' => function ($query) {
                $query->where('status_pembayaran', 'lunas');
            }
        ])
        ->orderByDesc('jumlah_transaksi_lunas')
        ->limit(3)
        ->get();
    }

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

    protected function getWidgets(): array
    {
        return [
            PendapatanGrooming::class,
            PendapatanPenitipan::class,
        ];
    }
}
