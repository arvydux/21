<x-layouts.app.header :title="$title ?? null">
    <flux:main class="bg-gradient-to-tr from-lime-600 via-emerald-600 to-teal-800">
        {{ $slot }}
    </flux:main>
</x-layouts.app.header>
