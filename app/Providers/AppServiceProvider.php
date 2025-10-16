<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set locale Carbon ke Bahasa Indonesia
        Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.utf8'); // Tambahan jika pakai strftime()

        // Filter log activity hanya untuk role tertentu
        Activity::saving(function (Activity $activity) {
            if (!Auth::check()) {
                return false;
            }

            $user = Auth::user();

            // Role yang boleh dilog aktivitasnya
            $allowedRoles = [
                'docter',
                'manager',
                'petshop_employee',
                'clinic_employee',
                'super_admin'
            ];

            if (!$user->hasAnyRole($allowedRoles)) {
                return false;
            }
        });
    }
}
