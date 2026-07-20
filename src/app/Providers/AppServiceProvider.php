<?php

namespace App\Providers;

use App\Services\VkSign;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(VkSign::class, function ($app): VkSign {
            return new VkSign((string) $app['config']->get('services.vk.secret'));
        });
    }

    public function boot(): void {}
}
