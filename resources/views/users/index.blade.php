<x-layouts.app active="users.index" :page-title="$pageTitle">
    <x-app.page-header
        title="Users & Roles"
        eyebrow="Management"
        :description="'Manage system accounts, roles, and access credentials. ' . $totalUsers . ' registered users across Super Admin, Admin, and User roles.'"
    >
        <x-slot:actions>
            <a href="{{ route('users.create') }}" class="inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white shadow-[0_6px_20px_-8px_rgba(201,0,22,0.8)] transition hover:bg-accent-soft active:scale-[0.98]">
                <x-app.icon name="plus" />
                Add New User
            </a>
        </x-slot:actions>
    </x-app.page-header>

    {{-- Summary cards --}}
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat-card label="Total Users" :value="$totalUsers" hint="accounts" icon="user" active />
        <x-ui.stat-card label="Super Admins" :value="$roleCounts['super_admin'] ?? 0" hint="full privileges" icon="user" accent />
        <x-ui.stat-card label="Admins" :value="$roleCounts['admin'] ?? 0" hint="catalog & sales" icon="user" />
        <x-ui.stat-card label="Standard Users" :value="$roleCounts['user'] ?? 0" hint="view-only" icon="user" />
    </div>

    {{-- Users table --}}
    <div class="overflow-hidden rounded-2xl border border-line bg-panel shadow-[0_1px_0_0_rgba(255,255,255,0.03)_inset]">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-accent/12 text-accent ring-1 ring-accent/20">
                    <x-app.icon name="user" />
                </span>
                <h2 class="text-[13px] font-semibold text-ink">Account Roster</h2>
            </div>
            <span class="rounded-lg bg-surface-strong px-2 py-1 text-[11px] text-muted ring-1 ring-line">
                <span class="font-semibold text-ink">{{ $totalUsers }}</span> users
            </span>
        </div>

        @if ($users->isEmpty())
            <div class="flex flex-col items-center gap-3 px-6 py-14 text-center">
                <span class="grid h-14 w-14 place-items-center rounded-2xl border border-line bg-surface-strong text-muted">
                    <x-app.icon name="user" />
                </span>
                <div>
                    <p class="text-[14px] font-semibold text-ink">No users found</p>
                    <p class="mt-1 max-w-sm text-[12.5px] leading-relaxed text-muted">
                        Add user accounts to grant access to the system.
                    </p>
                </div>
                <a href="{{ route('users.create') }}" class="mt-1 inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-[13px] font-semibold text-white transition hover:bg-accent-soft">
                    <x-app.icon name="plus" />
                    Add New User
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] border-collapse text-left">
                    <caption class="sr-only">Registered users with their roles and customize actions</caption>
                    <thead>
                        <tr class="border-b border-line bg-line/30">
                            <th scope="col" class="w-20 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">ID</th>
                            <th scope="col" class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">USER</th>
                            <th scope="col" class="w-48 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">ROLE</th>
                            <th scope="col" class="w-40 px-4 py-3 text-[10px] font-bold uppercase tracking-[0.14em] text-muted">CREATED</th>
                            <th scope="col" class="w-36 px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.14em] text-muted">CUSTOMIZE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line/70">
                        @foreach ($users as $record)
                            @php
                                $isSelf = $record->id === auth()->id();
                                $isLastSuperAdmin = $record->is(\App\Auth\Role::SuperAdmin) && ($roleCounts['super_admin'] ?? 0) <= 1;
                            @endphp
                            <tr class="group transition-colors duration-150 hover:bg-line/40">
                                <td class="px-4 py-3 align-middle">
                                    <span class="inline-grid h-8 min-w-8 place-items-center rounded-lg bg-surface-strong px-2 text-[12px] font-bold tabular-nums text-ink ring-1 ring-line transition group-hover:bg-accent/12 group-hover:text-accent group-hover:ring-accent/25">
                                        {{ $record->id }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    <div class="flex items-center gap-3">
                                        <span class="relative grid h-9 w-9 shrink-0 place-items-center overflow-hidden rounded-xl bg-accent/20 text-[12px] font-bold text-accent ring-1 ring-accent/25 transition group-hover:ring-accent/50">
                                            <svg viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 text-accent" aria-hidden="true">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                        </span>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="block truncate text-[13.5px] font-semibold text-ink">{{ $record->name }}</span>
                                                @if ($isSelf)
                                                    <span class="rounded bg-accent/10 px-1.5 py-0.5 text-[10px] font-bold text-accent">You</span>
                                                @endif
                                            </div>
                                            <span class="mt-0.5 block truncate text-[11px] text-muted">{{ $record->email }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    <x-ui.badge :label="$record->role->label()" :tone="$record->role->tone()" dot />
                                </td>

                                <td class="px-4 py-3 align-middle">
                                    <span class="text-[12.5px] text-muted">{{ $record->created_at ? \Illuminate\Support\Carbon::parse($record->created_at)->format('M d, Y') : '—' }}</span>
                                </td>

                                <td class="px-4 py-3 text-right align-middle">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('users.edit', $record->id) }}" title="Edit {{ $record->name }}" aria-label="Edit {{ $record->name }}" class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-surface-strong text-muted transition hover:border-accent/40 hover:text-accent">
                                            <x-app.icon name="edit" />
                                        </a>

                                        @if ($isSelf)
                                            <span class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-surface-strong/50 text-muted/40 cursor-not-allowed" title="You cannot delete your own account">
                                                <x-app.icon name="trash" />
                                            </span>
                                        @elseif ($isLastSuperAdmin)
                                            <span class="grid h-8 w-8 place-items-center rounded-lg border border-line bg-surface-strong/50 text-muted/40 cursor-not-allowed" title="The last Super Admin cannot be deleted">
                                                <x-app.icon name="trash" />
                                            </span>
                                        @else
                                            <x-app.confirm-form
                                                :action="route('users.destroy', $record->id)"
                                                method="DELETE"
                                                compact
                                                icon="trash"
                                                title="Delete this user?"
                                                :message="'<span class=&quot;font-semibold text-ink&quot;>'.e($record->name).'</span> will be removed from the system and will no longer be able to log in.'"
                                                confirm-label="Yes, delete"
                                            />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.app>
