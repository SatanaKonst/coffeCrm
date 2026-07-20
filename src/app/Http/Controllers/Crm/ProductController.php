<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->where('is_active', true)
            ->whereHas('prices')
            ->with('prices')
            ->orderBy('name')
            ->paginate(12);

        return view('crm.products.index', ['products' => $products]);
    }
}
