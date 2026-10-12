<x-layouts.guest :page-title="$pageTitle">
    <div class="rounded-2xl border border-line bg-panel p-6 shadow-2xl">
        <h1 class="text-[18px] font-bold tracking-tight text-ink">Sign in</h1>
        <p class="mt-1 text-[12.5px] text-muted">Use your catalog account to continue.</p>

        @error('email')
            <div class="mt-4 rounded-xl border border-rose-500/25 bg-rose-500/[0.07] px-3.5 py-2.5 text-[12.5px] text-rose-700">
                {{ $message }}
            </div>
        @enderror

        <form method="POST" action="{{ route('login.store') }}" class="mt-5 space-y-4">
            @csrf

            <div>
                <label for="email" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Email or Name</label>
                <input
                    id="email"
                    name="email"
                    type="text"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Admin"
                    @class([
                        'h-11 w-full rounded-xl border bg-surface-strong px-3.5 text-[13px] text-ink placeholder:text-muted transition focus:outline-none focus:ring-2',
                        'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('email'),
                        'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('email'),
                    ])
                >
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    @class([
                        'h-11 w-full rounded-xl border bg-surface-strong px-3.5 text-[13px] text-ink placeholder:text-muted transition focus:outline-none focus:ring-2',
                        'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('password'),
                        'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('password'),
                    ])
                >
            </div>

            <label class="flex items-center gap-2 text-[12.5px] text-muted">
                <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-line bg-surface-strong text-accent focus:ring-accent/30">
                Remember me on this device
            </label>

            <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white shadow-[0_8px_24px_-10px_rgba(201,0,22,0.9)] transition hover:bg-accent-soft active:scale-[0.99]">
                Sign in
            </button>
        </form>
    </div>
</x-layouts.guest>
