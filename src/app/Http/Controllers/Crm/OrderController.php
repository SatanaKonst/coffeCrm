<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\StoreOrderRequest;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        /** @var Client $client */
        $client = $request->attributes->get('client');

        $orders = $client->orders()
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('crm.orders.index', ['orders' => $orders]);
    }

    public function create(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        return view('crm.orders.create', ['product' => $product]);
    }

    public function store(StoreOrderRequest $request)
    {
        /** @var Client $client */
        $client = $request->attributes->get('client');

        /** @var Product $product */
        $product = Product::query()
            ->where('is_active', true)
            ->findOrFail($request->validated('product_id'));

        $qty = (int) $request->validated('qty');

        $order = \DB::transaction(function () use ($client, $product, $qty, $request): Order {
            $order = $client->orders()->create([
                'status' => 'new',
                'total' => $product->price * $qty,
                'comment' => $request->validated('comment'),
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'qty' => $qty,
            ]);

            return $order;
        });

        return redirect()
            ->route('crm.orders.index')
            ->with('success', "Заказ #{$order->id} оформлен. Скоро свяжемся для подтверждения.");
    }
}
