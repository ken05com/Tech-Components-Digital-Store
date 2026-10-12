@php
    $isEdit = $user !== null;
    $action = $isEdit ? route('users.update', $user->id) : route('users.store');
@endphp

<x-layouts.app active="users.index" :page-title="$pageTitle">
    <nav class="flex flex-wrap items-center gap-1.5 text-[11px] font-medium text-muted" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}" class="transition hover:text-accent">TECH COMPONENTS</a>
        <span class="text-muted">/</span>
        <a href="{{ route('users.index') }}" class="transition hover:text-accent">Users & Roles</a>
        <span class="text-muted">/</span>
        <span class="text-muted">{{ $isEdit ? 'Edit' : 'Add New' }}</span>
    </nav>

    <x-app.page-header
        :title="$isEdit ? 'Edit '.$user->name : 'Add New User'"
        eyebrow="Management"
        description="Create or modify system account credentials and role assignments."
    />

    <form method="POST" action="{{ $action }}" class="w-full space-y-5">
        @csrf
        @if ($isEdit) @method('PATCH') @endif

        <div class="rounded-2xl border border-line bg-panel p-5">
            <div class="mb-4 flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                    <x-app.icon name="user" />
                </span>
                <h2 class="text-[14px] font-semibold text-ink">Account Credentials</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Full Name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user?->name) }}"
                        required
                        maxlength="120"
                        placeholder="e.g. Jane Doe"
                        @class([
                            'h-11 w-full rounded-xl border bg-surface-strong px-3.5 text-[13px] text-ink placeholder:text-muted transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('name'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('name'),
                        ])
                    >
                    @error('name') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Email Address</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user?->email) }}"
                        required
                        maxlength="160"
                        placeholder="you@example.com"
                        @class([
                            'h-11 w-full rounded-xl border bg-surface-strong px-3.5 text-[13px] text-ink placeholder:text-muted transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('email'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('email'),
                        ])
                    >
                    @error('email') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="role" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">System Role</label>
                    <select
                        id="role"
                        name="role"
                        required
                        @class([
                            'h-11 w-full rounded-xl border bg-surface-strong px-3.5 text-[13px] text-ink transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('role'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('role'),
                        ])
                    >
                        @foreach ($roles as $roleCase)
                            <option value="{{ $roleCase->value }}" @selected(old('role', $user?->role->value) === $roleCase->value)>
                                {{ $roleCase->label() }} — @match ($roleCase) {
                                    App\Auth\Role::SuperAdmin => 'Full system access & settings',
                                    App\Auth\Role::Admin => 'Catalog and sales management',
                                    App\Auth\Role::User => 'Standard view-only access',
                                }
                            </option>
                        @endforeach
                    </select>
                    @error('role') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">
                        Password {{ $isEdit ? '(leave blank to keep current)' : '' }}
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        {{ $isEdit ? '' : 'required' }}
                        placeholder="••••••••"
                        @class([
                            'h-11 w-full rounded-xl border bg-surface-strong px-3.5 text-[13px] text-ink placeholder:text-muted transition focus:outline-none focus:ring-2',
                            'border-rose-500/50 focus:border-rose-500/60 focus:ring-rose-500/20' => $errors->has('password'),
                            'border-line focus:border-accent/50 focus:ring-accent/20' => ! $errors->has('password'),
                        ])
                    >
                    @error('password') <p class="mt-1.5 text-[11.5px] text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-muted">Confirm Password</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        {{ $isEdit ? '' : 'required' }}
                        placeholder="••••••••"
                        class="h-11 w-full rounded-xl border border-line bg-surface-strong px-3.5 text-[13px] text-ink placeholder:text-muted transition focus:border-accent/50 focus:outline-none focus:ring-2 focus:ring-accent/20"
                    >
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2">
            <a href="{{ route('users.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl px-4 text-[13px] font-semibold text-muted transition hover:bg-line/40 hover:text-ink">
                Cancel
            </a>
            <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white shadow-[0_8px_24px_-10px_rgba(201,0,22,0.9)] transition hover:bg-accent-soft active:scale-[0.99]">
                <x-app.icon name="plus" />
                {{ $isEdit ? 'Save changes' : 'Add New User' }}
            </button>
        </div>
    </form>
</x-layouts.app>
