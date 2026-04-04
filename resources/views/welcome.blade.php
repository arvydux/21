<x-layouts.app.header :title="$title ?? null">
    <flux:main class="bg-gradient-to-tr from-lime-600 via-emerald-600 to-teal-800 h-screen">
        <div class="h-full grid md:grid-cols-2 grid-rows-3 gap-4">

            <a href="{{ route('kasa') }}" class="block h-full">
                <div class="relative text-center shadow-md hover:drop-shadow-2xl hover:scale-101 h-full overflow-hidden rounded-xl bg-gray-900/20">
                    <div class="flex items-center justify-center h-full text-white/80">
                        <div class="font-semibold text-6xl">Kasa</div>
                    </div>
                </div>
            </a>

            <a href="{{ route('menu') }}" class="block h-full">
                <div class="relative text-center shadow-md hover:drop-shadow-2xl hover:scale-101 h-full overflow-hidden rounded-xl bg-gray-900/20">
                    <div class="flex items-center justify-center h-full text-white/80">
                        <div class="font-semibold text-6xl">Meniu</div>
                    </div>
                </div>
            </a>

            <a href="{{ route('kitchen') }}" class="block h-full">
                <div class="relative text-center shadow-md hover:drop-shadow-2xl hover:scale-101 h-full overflow-hidden rounded-xl bg-gray-900/20">
                    <div class="flex items-center justify-center h-full text-white/80">
                        <div class="font-semibold text-6xl">Virtuvė</div>
                    </div>
                </div>
            </a>

            <a href="{{ route('orders') }}" class="block h-full">
                <div class="relative text-center shadow-md hover:drop-shadow-2xl hover:scale-101 h-full overflow-hidden rounded-xl bg-gray-900/20">
                    <div class="flex items-center justify-center h-full text-white/80">
                        <div class="font-semibold text-6xl">Užsakymai</div>
                    </div>
                </div>
            </a>

            <a href="{{ route('settings') }}" class="block h-full md:col-span-2">
                <div class="relative text-center shadow-md hover:drop-shadow-2xl hover:scale-101 h-full overflow-hidden rounded-xl bg-gray-900/20">
                    <div class="flex items-center justify-center h-full text-white/80">
                        <div class="font-semibold text-6xl">Nustatymai</div>
                    </div>
                </div>
            </a>

        </div>
    </flux:main>
</x-layouts.app.header>
