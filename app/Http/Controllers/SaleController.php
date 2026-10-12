<?php

namespace App\Http\Controllers;

use App\Auth\Role;
use App\Sales\SaleRepository;
use Illuminate\Contracts\View\View;

class SaleController extends Controller
{
    public function __construct(private readonly SaleRepository $sales) {}

    public function index(): View
    {
        return view('sales.index', [
            'sales' => $this->sales->all(),
            'totalSales' => $this->sales->count(),
            'itemsSold' => $this->sales->itemsSold(),
            'revenue' => $this->sales->revenue(),
            'revenueToday' => $this->sales->revenueToday(),
            'pageTitle' => 'Sales',
        ]);
    }

    public function purchases(): View
    {
        $user = auth()->user();
        $sales = $this->sales->all();
        if ($user && ! $user->is(Role::SuperAdmin) && ! $user->is(Role::Admin)) {
            $sales = $sales->filter(fn ($s) => $s->customerEmail === $user->email || $s->userId === $user->id);
        }

        return view('sales.index', [
            'sales' => $sales,
            'totalSales' => $sales->count(),
            'itemsSold' => $sales->sum(fn ($s) => $s->itemsCount),
            'revenue' => $sales->sum(fn ($s) => $s->total),
            'revenueToday' => 0,
            'pageTitle' => 'My Purchase',
        ]);
    }

    public function show(int $sale): View
    {
        $record = $this->sales->find($sale);

        if ($record === null) {
            abort(404, 'Sale not found.');
        }

        $user = auth()->user();
        $canViewSales = $user?->can('view-sales');
        $isOwner = $user && ($record->customerEmail === $user->email || $record->userId === $user->id);

        if (! $canViewSales && ! $isOwner) {
            abort(403, 'This action is unauthorized.');
        }

        return view('sales.show', [
            'sale' => $record,
            'pageTitle' => 'Order '.$record->reference,
        ]);
    }
}
