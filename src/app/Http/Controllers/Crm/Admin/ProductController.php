<?php

namespace App\Http\Controllers\Crm\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\Admin\StoreProductRequest;
use App\Http\Requests\Crm\Admin\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with('prices')
            ->when($request->query('q'), fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->unless($request->has('inactive'), fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('crm.admin.products.index', [
            'products' => $products,
            'filter' => ['q' => $request->query('q'), 'inactive' => $request->has('inactive')],
        ]);
    }

    public function create()
    {
        return view('crm.admin.products.form', ['product' => new Product]);
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $product = Product::create($data);
        $this->syncPrices($product, $data['prices'] ?? []);

        return redirect()
            ->route('crm.admin.products.index')
            ->with('success', "Товар «{$product->name}» создан.");
    }

    public function edit(Product $product)
    {
        $product->load('prices');

        return view('crm.admin.products.form', ['product' => $product]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $product->update($data);

        if (isset($data['prices'])) {
            $this->syncPrices($product, $data['prices']);
        }

        return redirect()
            ->route('crm.admin.products.index')
            ->with('success', "Товар «{$product->name}» обновлён.");
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();

        return redirect()
            ->route('crm.admin.products.index')
            ->with('success', "Товар «{$name}» удалён.");
    }

    /**
     * Заменить все цены товара на переданные.
     *
     * @param  array<int, array{volume: string, price: numeric}>  $prices
     */
    private function syncPrices(Product $product, array $prices): void
    {
        $product->prices()->delete();

        $rows = array_map(fn (array $p) => [
            'volume' => $p['volume'],
            'price' => $p['price'],
            'product_id' => $product->id,
            'created_at' => now(),
            'updated_at' => now(),
        ], $prices);

        if ($rows) {
            $product->prices()->insert($rows);
        }
    }
}
