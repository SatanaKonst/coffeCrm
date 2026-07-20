<?php

namespace App\Http\Controllers\Crm;

use App\Enums\CoffeeVolume;
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

        $product->load('prices');

        // Доступные объёмы = те, для которых задана цена.
        $volumes = collect(CoffeeVolume::cases())
            ->filter(fn (CoffeeVolume $v) => $product->hasVolume($v))
            ->values();

        abort_if($volumes->isEmpty(), 404, 'У товара не заданы цены.');

        /** @var Client $client */
        $client = $request->attributes->get('client');

        return view('crm.orders.create', [
            'product' => $product,
            'client' => $client,
            'volumes' => $volumes,
        ]);
    }

    public function store(StoreOrderRequest $request)
    {
        /** @var Client $client */
        $client = $request->attributes->get('client');
        $data = $request->validated();

        /** @var Product $product */
        $product = Product::query()
            ->where('is_active', true)
            ->findOrFail($data['product_id']);

        $volume = CoffeeVolume::from($data['volume']);
        $unitPrice = $product->priceFor($volume);

        // Объём должен быть доступен (цена задана).
        if ($unitPrice === null) {
            return back()->withInput()->withErrors(
                ['volume' => "Товар недоступен в объёме «{$volume->label()}»."],
            );
        }

        $qty = (int) $data['qty'];
        $total = round($unitPrice * $qty, 2);

        $order = \DB::transaction(function () use ($client, $product, $data, $qty, $unitPrice, $total, $volume): Order {
            // Обновляем профиль клиента актуальными данными.
            $client->update([
                'name' => $data['client_name'],
                'phone' => $data['client_phone'],
            ]);

            $order = $client->orders()->create([
                'client_name' => $data['client_name'],
                'client_phone' => $data['client_phone'],
                'status' => 'new',
                'total' => $total,
                'comment' => $data['comment'] ?? null,
                'city' => $data['city'],
                'street' => $data['street'],
                'building' => $data['building'],
                'entrance' => $data['entrance'] ?? null,
                'apartment' => $data['apartment'] ?? null,
                'intercom' => $data['intercom'] ?? null,
                'subscription' => $data['subscription'],
                'volume' => $volume,
                'grind' => $data['grind'] ?? false,
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $unitPrice,
                'qty' => $qty,
            ]);

            return $order;
        });

        return redirect()
            ->route('crm.orders.index')
            ->with('success', "Заказ #{$order->id} оформлен. Скоро свяжемся для подтверждения.");
    }
}
