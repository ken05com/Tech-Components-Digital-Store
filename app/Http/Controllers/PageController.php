<?php

namespace App\Http\Controllers;

use App\Catalog\CategoryRecord;
use App\Catalog\CategoryRepository;
use App\Catalog\ProductRepository;
use App\Settings\SettingRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly CategoryRepository $categories,
        private readonly SettingRepository $settings,
    ) {}

    public function settings(): View
    {
        $categories = $this->products->categories();

        return view('settings', [
            'categories' => $categories,
            'counts' => $categories->mapWithKeys(
                fn (CategoryRecord $c) => [$c->value => $this->products->countByCategory($c)]
            ),
            'totalProducts' => $this->products->count(),
            'deletedCount' => $this->products->countTrashed(),
            'hasLocalChanges' => $this->products->hasLocalChanges(),
            'author' => $this->settings->author(),
            'reflections' => $this->settings->reflections(),
            'pageTitle' => 'Settings',
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'author_name' => ['required', 'string', 'max:120'],
            'author_address' => ['nullable', 'string', 'max:255'],
            'author_image' => ['nullable', 'string', 'max:255'],
            'reflection_1_title' => ['required', 'string', 'max:160'],
            'reflection_1_body' => ['required', 'string', 'max:1000'],
            'reflection_2_title' => ['required', 'string', 'max:160'],
            'reflection_2_body' => ['required', 'string', 'max:1000'],
            'reflection_3_title' => ['required', 'string', 'max:160'],
            'reflection_3_body' => ['required', 'string', 'max:1000'],
        ]);

        $this->settings->putMany($validated);
        $this->settings->flush();

        return redirect()
            ->route('settings')
            ->with('success_dialog', 'Settings saved');
    }

    public function about(): View
    {
        return view('about', [
            'author' => $this->settings->author(),
            'entries' => [
                [
                    'label' => 'I chose the Tech Components topic because',
                    'body' => 'Tech Components is a hands-on subject: every part of a computer — processor, memory, storage, networking and power — is something you can actually touch, compare and price. Building the catalog around real products, stock levels and sales let me demonstrate the whole idea end to end, from browsing a storefront to managing inventory from one system.',
                ],
                [
                    'label' => 'My 2nd option topic is about Computers',
                    'body' => 'Computers was my second choice. It covers the bigger picture — how hardware and software work together as a complete machine — but that scope is so wide that a single project can only scratch the surface. Tech Components keeps the same real-world appeal while staying focused enough to build and finish properly.',
                ],
                [
                    'label' => 'I am proud of my topic because',
                    'body' => 'it sits right inside my comfort zone of expertise. Hardware is what I enjoy and understand best, so I could design features that mirror how a real computer shop runs — categories, product variants, stock and checkout — instead of forcing an area I was unsure about. That confidence carries into the detail of the build and makes every decision easy to explain.',
                ],
            ],
            'pageTitle' => 'About',
        ]);
    }

    public function profile(): View
    {
        return view('profile', [
            'user' => Auth::user(),
            'author' => $this->settings->author(),
            'totalProducts' => $this->products->count(),
            'categoryCount' => $this->categories->count(),
            'deletedCount' => Auth::user()?->can('view-deleted') ? $this->products->countTrashed() : null,
            'pageTitle' => 'Profile',
        ]);
    }

    public function notifications(): View
    {
        return view('notifications.index', [
            'pageTitle' => 'Notifications',
        ]);
    }

    public function vouchers(): View
    {
        return view('vouchers.index', [
            'pageTitle' => 'My Vouchers',
        ]);
    }

    /** Restores the 25 rows exactly as they appear in the source document. */
    public function resetCatalog(): RedirectResponse
    {
        $this->products->reset();

        return redirect()
            ->route('settings')
            ->with('success_dialog', 'Catalog restored to the original 25 products');
    }
}
