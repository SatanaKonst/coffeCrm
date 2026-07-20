<?php

namespace App\Providers;

use App\Services\VkSign;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(VkSign::class, function ($app): VkSign {
            return new VkSign((string) $app['config']->get('services.vk.secret'));
        });
    }

    public function boot(): void
    {
        View::composer('crm.*', function (\Illuminate\View\View $view): void {
            $request = $view->getData()['request'] ?? request();

            $view->with([
                'client' => $request->attributes->get('client'),
                'isAdmin' => $request->attributes->get('isAdmin', false),
            ]);
        });
    }
}
