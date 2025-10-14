<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use App\Http\Middleware\CrossOriginOpenerPolicy;

class HttpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(HttpKernel $kernel): void
    {
        $kernel->pushMiddleware(CrossOriginOpenerPolicy::class);
    }
}
