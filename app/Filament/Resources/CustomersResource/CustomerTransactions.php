<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Models\Customer;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class CustomerTransactions extends Page
{
    use InteractsWithRecord;

    protected static string $resource = CustomerResource::class;

    protected static string $view = 'filament.resources.customer-resource.pages.customer-transactions';

    protected static ?string $navigationLabel = null;

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
            'transaksis' => $this->record
                ->transaksi() // relasi ke tabel transaksi
                ->where('status_pembayaran', 'lunas')
                ->orderByDesc('tanggal_transaksi')
                ->get(),
        ];
    }
}
