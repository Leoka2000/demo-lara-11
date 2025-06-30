<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="relative aspect-video overflow-hidden bg-gray-50 rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full " />
                <livewire:temperature />
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border bg-gray-50 border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full " />
                <livewire:batteryvoltage />

            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border bg-gray-50 border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full " />
                <livewire:chartbattery />
            </div>
        </div>
        <div
            class="relative h-full flex-1 overflow-hidden rounded-xl border bg-gray-50 border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full " />
            <livewire:historicaldata />
        </div>
    </div>
</x-layouts.app>