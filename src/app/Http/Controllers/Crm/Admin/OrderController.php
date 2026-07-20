<?php

namespace App\Http\Controllers\Crm\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $statuses = OrderStatus::cases();

        $orders = Order::query()
            ->with(['client', 'items'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->query('q'), function ($q, $term): void {
                $q->whereHas('client', fn ($cq) => $cq->where('name', 'like', "%{$term}%"))
                    ->orWhere('id', $term);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('crm.admin.orders.index', [
            'orders' => $orders,
            'statuses' => $statuses,
            'filter' => ['status' => $request->query('status'), 'q' => $request->query('q')],
        ]);
    }

    public function update(UpdateOrderStatusRequest $request, Order $order)
    {
        $order->update(['status' => $request->validated('status')]);

        return redirect()
            ->route('crm.admin.orders.index', $request->only('status', 'q'))
            ->with('success', "Заказ #{$order->id} обновлён.");
    }
}
