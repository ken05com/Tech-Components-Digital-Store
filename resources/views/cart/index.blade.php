<x-layouts.storefront>

    <div class="mx-auto w-full max-w-[1200px] px-4 py-8 sm:px-6 lg:px-10">

        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-brand">Shopping cart</p>
                <h1 class="mt-1 text-[24px] font-extrabold tracking-tight text-ink">Your Cart</h1>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-[12.5px] font-semibold text-muted transition hover:text-brand">
                <x-storefront.icon name="chevron-left" class="h-4 w-4" />
                Continue shopping
            </a>
        </div>

        @if (session('status'))
            <div class="mb-5 rounded-xl border border-line-soft bg-surface px-4 py-3 text-[13px] text-ink">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-xl border border-rose-500/30 bg-rose-500/[0.06] px-4 py-3 text-[13px] text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        @if (count($lines) === 0)
            <div class="grid place-items-center rounded-2xl border border-dashed border-line-soft bg-surface px-6 py-20 text-center">
                <span class="grid h-14 w-14 place-items-center rounded-full bg-brand/10 text-brand">
                    <x-storefront.icon name="cart" class="h-6 w-6" />
                </span>
                <p class="mt-4 text-[15px] font-bold text-ink">Your cart is empty</p>
                <p class="mt-1 text-[13px] text-muted">Browse the catalog and add a few gadgets to get started.</p>
                <a href="{{ route('products.index') }}" class="mt-5 inline-flex h-11 items-center rounded-full bg-brand px-6 text-[13px] font-bold text-white transition hover:bg-brand-dark">
                    Shop products
                </a>
            </div>
        @else
            <div class="grid gap-6 lg:grid-cols-[1fr_340px]">
                <div class="space-y-3">
                    @foreach ($lines as $line)
                        @php $product = $line['product']; @endphp
                        <div class="flex items-center gap-4 rounded-2xl border border-line-soft bg-white p-3.5">
                            <a href="{{ route('products.detail', $product->id) }}" class="grid h-20 w-20 shrink-0 place-items-center overflow-hidden rounded-xl border border-line-soft bg-surface">
                                <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-contain p-2" onerror="this.src='{{ asset('images/products/placeholder.svg') }}'">
                            </a>

                            <div class="min-w-0 flex-1">
                                <a href="{{ route('products.detail', $product->id) }}" class="line-clamp-2 text-[13.5px] font-semibold text-ink transition hover:text-brand">{{ $product->name }}</a>
                                <p class="mt-0.5 text-[11px] font-semibold uppercase tracking-[0.1em] text-muted">{{ $product->category->shortLabel() }}</p>
                                @if ($line['variant'])
                                    <p class="mt-0.5 text-[11.5px] text-muted">Variant: <span class="font-semibold text-ink">{{ $line['variant'] }}</span></p>
                                @endif
                                <p class="mt-1 text-[13px] font-bold text-brand">{{ $line['unit_price'] > 0 ? '₱'.number_format($line['unit_price']) : $product->priceLabel() }}</p>
                            </div>

                            <form method="POST" action="{{ route('cart.update', $product->id) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="variant" value="{{ $line['variant'] }}">
                                <input
                                    type="number"
                                    name="quantity"
                                    value="{{ $line['quantity'] }}"
                                    min="0"
                                    max="{{ max(0, $product->stock) }}"
                                    class="h-9 w-16 rounded-lg border border-line-soft bg-surface text-center text-[13px] tabular-nums text-ink focus:border-brand focus:outline-none"
                                    aria-label="Quantity for {{ $product->name }}"
                                >
                                <button type="submit" class="grid h-9 w-9 place-items-center rounded-lg bg-surface text-muted transition hover:text-brand" aria-label="Update quantity">
                                    <x-storefront.icon name="refresh" class="h-4 w-4" />
                                </button>
                            </form>

                            <div class="w-24 text-right">
                                <p class="text-[14px] font-extrabold tabular-nums text-ink">₱{{ number_format($line['subtotal']) }}</p>
                            </div>

                            <form method="POST" action="{{ route('cart.destroy', $product->id) }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="variant" value="{{ $line['variant'] }}">
                                <button type="submit" class="grid h-9 w-9 place-items-center rounded-lg text-muted transition hover:bg-rose-500/10 hover:text-rose-600" aria-label="Remove {{ $product->name }}">
                                    <x-app.icon name="trash" />
                                </button>
                            </form>
                        </div>
                    @endforeach

                    <form method="POST" action="{{ route('cart.clear') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-[12px] font-semibold text-muted transition hover:text-rose-600">Clear cart</button>
                    </form>
                </div>

                <aside class="h-fit rounded-2xl border border-line-soft bg-surface p-5 lg:sticky lg:top-24">
                    <h2 class="text-[14px] font-bold text-ink">Order summary</h2>
                    <dl class="mt-4 space-y-2 text-[13px]">
                        <div class="flex items-center justify-between">
                            <dt class="text-muted">Items</dt>
                            <dd class="font-semibold tabular-nums text-ink">{{ collect($lines)->sum('quantity') }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-muted">Subtotal</dt>
                            <dd class="font-semibold tabular-nums text-ink">₱{{ number_format($subtotal) }}</dd>
                        </div>
                    </dl>
                    <div class="mt-4 flex items-center justify-between border-t border-line-soft pt-4">
                        <span class="text-[13px] font-bold text-ink">Total</span>
                        <span class="text-[18px] font-extrabold tabular-nums text-brand">₱{{ number_format($subtotal) }}</span>
                    </div>
                    <a href="{{ route('checkout.create') }}" class="mt-5 inline-flex h-12 w-full items-center justify-center gap-2 rounded-full bg-brand text-[13.5px] font-bold text-white transition hover:bg-brand-dark">
                        Proceed to checkout
                        <x-storefront.icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </aside>
            </div>
        @endif
    </div>

</x-layouts.storefront>
