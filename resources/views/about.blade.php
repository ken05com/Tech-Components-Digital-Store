<x-layouts.app active="about" :page-title="$pageTitle">
    {{-- Centered page title --}}
    <header class="pt-2 text-center">
        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-accent">About</p>
        <h1 class="mt-2 text-[30px] font-bold tracking-tight text-ink sm:text-4xl">Tech Components</h1>
        <p class="mx-auto mt-3 max-w-2xl text-[13px] leading-relaxed text-muted">
            The story behind this catalog — why it was chosen, what the alternative was, and why it fits the author best.
        </p>
    </header>

    {{-- User profile --}}
    <section class="mx-auto max-w-3xl rounded-2xl border border-line bg-panel p-6">
        <div class="flex flex-col items-center gap-5 text-center sm:flex-row sm:text-left">
            <span class="grid h-24 w-24 shrink-0 place-items-center overflow-hidden rounded-2xl bg-accent/12 text-accent ring-1 ring-accent/25">
                @if (! empty($author['image']))
                    <img src="{{ $author['image'] }}" alt="{{ $author['name'] }} profile picture" class="h-full w-full object-cover">
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="h-10 w-10" aria-hidden="true">
                        <circle cx="12" cy="9" r="3.5" />
                        <path d="M4.5 20a7.5 7.5 0 0 1 15 0" stroke-linecap="round" />
                    </svg>
                @endif
            </span>
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-muted">Name</p>
                <p class="mt-1 truncate text-[18px] font-bold text-ink">{{ $author['name'] !== '' ? $author['name'] : 'Your Name' }}</p>

                <p class="mt-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-muted">Address</p>
                <p class="mt-1 text-[13px] text-muted">{{ $author['address'] !== '' ? $author['address'] : 'Your address' }}</p>
            </div>
        </div>
    </section>

    {{-- Text content --}}
    <section class="mx-auto max-w-3xl">
        <ol class="space-y-3">
            @foreach ($entries as $entry)
                <li class="flex gap-4 rounded-2xl border border-line bg-panel p-5">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-accent/12 text-[13px] font-bold text-accent ring-1 ring-accent/25">{{ $loop->iteration }}</span>
                    <div class="min-w-0">
                        <p class="text-[13.5px] font-semibold text-ink">{{ $entry['label'] }}</p>
                        <p class="mt-1 text-[12.5px] leading-relaxed text-muted">{{ $entry['body'] }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>
</x-layouts.app>
