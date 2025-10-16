<?php

use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\OwnerPanelProvider;

return [

    /*
    |--------------------------------------------------------------------------
    | Filament Panels
    |--------------------------------------------------------------------------
    |
    | Here you may register as many panels as you want.
    |
    */

    'panels' => [
        AdminPanelProvider::class,
        OwnerPanelProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | This is the storage disk Filament will use to store files. You may use
    | any of the disks defined in `config/filesystems.php`.
    |
    */

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Assets Path
    |--------------------------------------------------------------------------
    |
    | This is the directory where Filament's assets will be published to.
    | It is relative to the `public` directory of your Laravel app.
    | After changing the path, run `php artisan filament:assets`.
    |
    */

    'assets_path' => null,

    /*
    |--------------------------------------------------------------------------
    | Cache Path
    |--------------------------------------------------------------------------
    |
    | This is where Filament will store cache files for optimization.
    | After changing this path, run `php artisan filament:cache-components`.
    |
    */

    'cache_path' => base_path('bootstrap/cache/filament'),

    /*
    |--------------------------------------------------------------------------
    | Broadcasting (optional)
    |--------------------------------------------------------------------------
    |
    | Uncomment if you want to enable broadcasting for real-time updates.
    |
    */

    'broadcasting' => [
        // 'echo' => [
        //     'broadcaster' => 'pusher',
        //     'key' => env('VITE_PUSHER_APP_KEY'),
        //     'cluster' => env('VITE_PUSHER_APP_CLUSTER'),
        //     'wsHost' => env('VITE_PUSHER_HOST'),
        //     'wsPort' => env('VITE_PUSHER_PORT'),
        //     'wssPort' => env('VITE_PUSHER_PORT'),
        //     'authEndpoint' => '/broadcasting/auth',
        //     'disableStats' => true,
        //     'encrypted' => true,
        //     'forceTLS' => true,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guard
    |--------------------------------------------------------------------------
    |
    | The guard filament will use for authentication.
    | Usually `web` for default Laravel session.
    |
    */

    'auth' => [
        'guard' => 'web',
    //     'pages' => [
    //     'login' => null, // akan redirect ke /login 
    // ],
],

    /*
    |--------------------------------------------------------------------------
    | Livewire Loading Delay
    |--------------------------------------------------------------------------
    |
    | Delay before loading indicators appear on Livewire requests.
    |
    */

    'livewire_loading_delay' => 'default',

];
