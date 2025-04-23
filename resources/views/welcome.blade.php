<x-layouts.app.header :title="$title ?? null">
    <flux:main class="bg-gradient-to-tr from-lime-600 via-emerald-600 to-teal-800">
        <div class="flex h-full w-full flex-1  gap-4 rounded-xl">
            <div class="grid md:grid-cols-2 auto-rows-min gap-4">

            <div class="auto-rows-min gap-4" data-tabs-toggle="#default-tab-content" role="tablist">
                <div class="grid md:grid-cols-1 auto-rows-min gap-4">
                    <a href="{{ route('kasa') }}" >
                        <div
                             class="relative text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
                aspect-video overflow-hidden rounded-xl  bg-gray-900/20 dark:border-neutral-700">
                            <div class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-xl w-full">
                                <div class="font-semibold text-9xl">Kasa</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="auto-rows-min gap-4">
                <div class="grid md:grid-cols-1 auto-rows-min gap-4">
                    <a href="{{ route('menu') }}" >
                    <div
                        class="relative text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
            aspect-video overflow-hidden rounded-xl  bg-gray-900/20 dark:border-neutral-700">
                        <div class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-xl w-full">
                            <div class="font-semibold text-9xl">Meniu</div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>

            <div class="auto-rows-min gap-4">
                <div class="grid md:grid-cols-1 auto-rows-min gap-4">
                    <a href="{{ route('orders') }}" >
                        <div
                            class="relative text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
            aspect-video overflow-hidden rounded-xl  bg-gray-900/20 dark:border-neutral-700">
                            <div class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-xl w-full">
                                <div class="font-semibold text-9xl">Užsakymai</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            </div>
        </div>
    </flux:main>
</x-layouts.app.header>
