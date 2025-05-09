<x-layouts.app.header :title="$title ?? null">
    @if (!Route::is('kitchen'))
        <!-- Code specific to the "kitchen" route -->
    <flux:main class="bg-gradient-to-tr from-lime-600 via-emerald-600 to-teal-800">
        {{ $slot }}
    </flux:main>
    @else
        <flux:main>
            {{ $slot }}
        </flux:main>
    @endif
</x-layouts.app.header>
