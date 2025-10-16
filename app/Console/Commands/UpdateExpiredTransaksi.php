<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaksi;
use Carbon\Carbon;

class UpdateExpiredTransaksi extends Command
{
    protected $signature = 'transaksi:update-expired';
    protected $description = 'Update status transaksi Menunggu menjadi Gagal jika sudah lewat 20 menit';

    public function handle()
    {
        $updated = Transaksi::where('status_pembayaran', 'Menunggu')
            ->where('token_expired_at', '<', Carbon::now())
            ->update(['status_pembayaran' => 'Gagal']);

        $this->info("Transaksi gagal diupdate: $updated");
    }
}

