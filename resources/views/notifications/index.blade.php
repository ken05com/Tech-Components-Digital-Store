<x-layouts.app active="notifications.index" :page-title="$pageTitle">
    <x-app.page-header
        title="Notifications"
        eyebrow="Account"
        description="Stay updated with your order statuses, store announcements, and system alerts."
    />

    <div class="rounded-2xl border border-line bg-white p-6 shadow-sm">
        <div class="flex flex-col items-center gap-3 py-12 text-center">
            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-surface-strong text-muted">
                <x-app.icon name="info" />
            </span>
            <div>
                <p class="text-[14px] font-semibold text-ink">No new notifications</p>
                <p class="mt-1 max-w-sm text-[12.5px] text-muted">You're all caught up! Order updates and announcements will appear here.</p>
            </div>
        </div>
    </div>
</x-layouts.app>
