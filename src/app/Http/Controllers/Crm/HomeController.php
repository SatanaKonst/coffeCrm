<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $client = $request->attributes->get('client');
        $isAdmin = $request->attributes->get('isAdmin', false);

        $stats = $isAdmin ? $this->adminStats() : $this->clientStats($client->id);

        return view('crm.dashboard', ['stats' => $stats]);
    }

    private function adminStats(): array
    {
        return [
            'Новые заказы' => [
                'value' => Order::where('status', 'new')->count(),
                'route' => route('crm.admin.orders.index', ['status' => 'new']),
                'color' => 'warning',
            ],
            'Всего заказов' => [
                'value' => Order::count(),
                'route' => route('crm.admin.orders.index'),
                'color' => 'primary',
            ],
            'Активных товаров' => [
                'value' => Product::where('is_active', true)->count(),
                'route' => route('crm.admin.products.index'),
                'color' => 'success',
            ],
        ];
    }

    private function clientStats(int $clientId): array
    {
        return [
            'Мои заказы' => [
                'value' => Order::where('client_id', $clientId)->count(),
                'route' => route('crm.orders.index'),
                'color' => 'primary',
            ],
            'Товаров в каталоге' => [
                'value' => Product::where('is_active', true)->count(),
                'route' => route('crm.products.index'),
                'color' => 'success',
            ],
        ];
    }
}
