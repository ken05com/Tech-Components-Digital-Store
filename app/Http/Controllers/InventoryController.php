<?php

namespace App\Http\Controllers;

use App\Catalog\ProductRepository;
use Illuminate\Contracts\View\View;

class InventoryController extends Controller
{
    public function __construct(private readonly ProductRepository $products) {}

    public function index(): View
    {
        $products = $this->products->all()
            ->sortBy(fn ($product) => $product->stock)
            ->values();

        return view('inventory.index', [
            'products' => $products,
            'totalStock' => $this->products->totalStock(),
            'stockValue' => $this->products->stockValue(),
            'lowStockCount' => $this->products->lowStockCount(),
            'outOfStockCount' => $this->products->outOfStockCount(),
            'threshold' => ProductRepository::LOW_STOCK_THRESHOLD,
            'pageTitle' => 'Inventory',
        ]);
    }
}
