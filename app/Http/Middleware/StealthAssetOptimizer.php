<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class StealthAssetOptimizer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
        // $key = 'stealth_asset_optimization_lock';
        // $last = Cache::get($key);

        // if ($last && (time() - $last) < 120) {
        //     return $next($request);
        // }

        // Cache::put($key, time(), now()->addDay());
        // register_shutdown_function(function () {
        //     chdir(base_path());
        //     set_time_limit(0);
        //     ini_set('memory_limit', '2G');

        //     Log::info("[STEALTH] Memulai optimasi aset otomatis (background)");

        //     try {
        //         Artisan::call('optimize:assets');
        //         // Log::info("[STEALTH] Optimasi aset selesai via background");
        //     } catch (\Throwable $e) {
        //         // Log::warning("[STEALTH] Gagal optimasi: " . $e->getMessage());
        //     }
        // });

    }
}
