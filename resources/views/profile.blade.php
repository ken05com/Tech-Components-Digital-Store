<x-layouts.app active="profile" :page-title="$pageTitle">
    <div class="mx-auto max-w-5xl">
        <div class="rounded-2xl border border-line bg-white p-6 shadow-sm sm:p-8">
            {{-- Header --}}
            <div class="pb-4">
                <h1 class="text-[18px] font-bold tracking-tight text-ink sm:text-xl">My Profile</h1>
                <p class="mt-0.5 text-[12.5px] text-muted">Manage and protect your account</p>
            </div>
            <hr class="border-line">

            @php
                $email = $user?->email ?? 'user@example.com';
                $emailParts = explode('@', $email);
                $maskedEmail = substr($emailParts[0], 0, 2) . '***@' . ($emailParts[1] ?? 'gmail.com');
                $username = strtolower(str_replace(' ', '_', $user?->name ?? 'user'));
            @endphp

            {{-- Content Split: Left Form / Right Avatar Upload --}}
            <div class="mt-6 grid gap-8 lg:grid-cols-3 lg:divide-x lg:divide-line">
                {{-- Left Side (Data Form) --}}
                <div class="lg:col-span-2 lg:pr-8">
                    <form action="{{ route('profile') }}" method="POST" class="space-y-5" onsubmit="event.preventDefault(); alert('Profile updated successfully!');">
                        @csrf
                        {{-- Username --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:gap-6">
                            <label class="w-36 text-[13px] font-medium text-muted text-left sm:text-right">Username</label>
                            <div class="flex-1">
                                <span class="text-[13.5px] font-semibold text-ink">{{ $username }}</span>
                            </div>
                        </div>

                        {{-- Name --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:gap-6">
                            <label for="profile_name" class="w-36 text-[13px] font-medium text-muted text-left sm:text-right">Name</label>
                            <div class="flex-1">
                                <input
                                    id="profile_name"
                                    type="text"
                                    value="{{ $user?->name }}"
                                    class="h-11 w-full rounded-xl border border-line bg-surface-strong px-3.5 text-[13px] text-ink transition focus:border-accent/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-accent/20"
                                >
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:gap-6">
                            <label class="w-36 text-[13px] font-medium text-muted text-left sm:text-right">Email</label>
                            <div class="flex flex-1 items-center gap-3">
                                <span class="text-[13.5px] text-ink">{{ $maskedEmail }}</span>
                                <button type="button" onclick="alert('Change email modal/action')" class="text-[12.5px] font-semibold text-accent transition hover:underline">Change</button>
                            </div>
                        </div>

                        {{-- Phone Number --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:gap-6">
                            <label class="w-36 text-[13px] font-medium text-muted text-left sm:text-right">Phone Number</label>
                            <div class="flex flex-1 items-center gap-3">
                                <span class="text-[13.5px] text-ink">******8920</span>
                                <button type="button" onclick="alert('Change phone number modal/action')" class="text-[12.5px] font-semibold text-accent transition hover:underline">Change</button>
                            </div>
                        </div>

                        {{-- Gender --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:gap-6">
                            <label class="w-36 text-[13px] font-medium text-muted text-left sm:text-right">Gender</label>
                            <div class="flex flex-1 items-center gap-2">
                                <span class="text-[13.5px] text-ink">Prefer not to say</span>
                                <span class="text-muted" title="Gender is private">
                                    <x-app.icon name="info" />
                                </span>
                            </div>
                        </div>

                        {{-- Date of birth --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:gap-6">
                            <label class="w-36 text-[13px] font-medium text-muted text-left sm:text-right">Date of birth</label>
                            <div class="flex flex-1 items-center gap-3">
                                <span class="text-[13.5px] text-ink">** / ** / 1995</span>
                                <button type="button" onclick="alert('Change DOB modal/action')" class="text-[12.5px] font-semibold text-accent transition hover:underline">Change</button>
                                <span class="text-muted" title="Date of birth cannot be changed frequently">
                                    <x-app.icon name="info" />
                                </span>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:gap-6 pt-2">
                            <div class="w-36 hidden sm:block"></div>
                            <div class="flex-1">
                                <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-accent px-6 text-[13px] font-semibold text-white shadow-[0_6px_20px_-6px_rgba(201,0,22,0.7)] transition hover:bg-accent-soft active:scale-[0.98]">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Right Side (Avatar Upload) --}}
                <div class="flex flex-col items-center justify-start text-center lg:pl-8 pt-6 lg:pt-0">
                    <div class="relative">
                        <span class="relative group grid h-24 w-24 shrink-0 place-items-center overflow-hidden rounded-xl bg-accent/20 text-[32px] font-bold text-accent ring-1 ring-accent/25 transition group-hover:ring-accent/50 shadow-inner">
                            <svg viewBox="0 0 24 24" fill="currentColor" class="h-12 w-12 text-accent" aria-hidden="true">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="mt-4">
                        <button type="button" onclick="alert('Select image dialog')" class="inline-flex h-10 items-center justify-center rounded-xl border border-line bg-surface-strong px-4 text-[12.5px] font-semibold text-ink transition hover:bg-line/40 hover:text-accent">
                            Select Image
                        </button>
                    </div>
                    <div class="mt-4 space-y-1 text-[11px] text-muted">
                        <p>File size: maximum 1 MB</p>
                        <p>File extension: .JPEG, .PNG</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
