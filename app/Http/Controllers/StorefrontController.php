<?php

namespace App\Http\Controllers;

use App\Catalog\CategoryRepository;
use App\Catalog\ProductRepository;
use Illuminate\Contracts\View\View;

/**
 * Public "Tech Components" storefront pages. Read-only access to the catalog.
 */
class StorefrontController extends Controller
{
    /**
     * The editorial inspiration cards shown on the home page.
     *
     * @var array{title: string, count: int, icon: string, bg: string, blurb: string}[]
     */
    private const IDEAS = [
        'tech-setup' => [
            'title' => 'Tech Setup Ideas',
            'count' => 128,
            'icon' => 'monitor',
            'bg' => 'from-slate-600 to-slate-900',
            'blurb' => 'Monitors, docks and the desk gear behind a great setup.',
        ],
        'travel-tech' => [
            'title' => 'Travel Tech Essentials',
            'count' => 96,
            'icon' => 'plane',
            'bg' => 'from-sky-500 to-slate-900',
            'blurb' => 'Portable storage and power essentials for life on the move.',
        ],
        'smart-home' => [
            'title' => 'Smart Home Inspiration',
            'count' => 74,
            'icon' => 'router',
            'bg' => 'from-emerald-500 to-slate-900',
            'blurb' => 'Connected ideas for a smarter home network.',
        ],
        'gaming-gear' => [
            'title' => 'Gaming Gear',
            'count' => 155,
            'icon' => 'gamepad',
            'bg' => 'from-violet-500 to-slate-900',
            'blurb' => 'Components and accessories built for play.',
        ],
        'minimal-desk' => [
            'title' => 'Minimal Desk Setup',
            'count' => 87,
            'icon' => 'cpu',
            'bg' => 'from-amber-500 to-slate-900',
            'blurb' => 'Clean, clutter-free workspaces you can build on your own.',
        ],
    ];

    public function __construct(
        private readonly ProductRepository $products,
        private readonly CategoryRepository $categories,
    ) {}

    public function index(): View
    {
        return view('storefront.index', [
            'trending' => $this->products->all()->take(6)->values(),
            'categories' => $this->categories->all(),
            'ideas' => self::IDEAS,
            'totalProducts' => $this->products->count(),
            'pageTitle' => '',
        ]);
    }

    public function today(): View
    {
        return view('storefront.placeholder', [
            'eyebrow' => 'Today',
            'title' => 'What’s new today',
            'message' => 'Personalized picks, fresh drops and the newest arrivals will live here. This page is a placeholder until the “Today” feed is wired up.',
            'pageTitle' => 'Today',
        ]);
    }

    public function following(): View
    {
        return view('storefront.placeholder', [
            'eyebrow' => 'Following',
            'title' => 'Your feed',
            'message' => 'Updates from your favorite categories and products will appear here. This page is a placeholder until the “Following” feed is wired up.',
            'pageTitle' => 'Following',
        ]);
    }

    public function ideas(): View
    {
        return view('storefront.ideas', [
            'ideas' => self::IDEAS,
            'pageTitle' => 'Ideas for Your Next Upgrade',
        ]);
    }

    public function idea(string $slug): View
    {
        $idea = self::IDEAS[$slug] ?? null;

        if ($idea === null) {
            abort(404, 'Unknown idea.');
        }

        return view('storefront.idea', [
            'idea' => $idea,
            'slug' => $slug,
            'pageTitle' => $idea['title'],
        ]);
    }
}
