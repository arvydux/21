<div class="grid md:grid-cols-2 auto-rows-min gap-4">
    <div class="grid md:grid-cols-3 auto-rows-min gap-4 mr-8">
        <div class="col-span-3">
            <div class="flex grid content-center text-white/80 rounded-xl ">
                <div class="font-semibold text-8xl">Gaminami:</div>
            </div>
        </div>
        @for ($i = 1; $i <= 15; $i++)
            <div class="relative text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
                aspect-video overflow-hidden rounded-xl bg-yellow-400/90 dark:border-neutral-700">
            <div class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-xl w-full">
                <div class="font-semibold text-8xl">{{ $i }}</div>
            </div>
        </div>
        @endfor
    </div>
    <div class="grid md:grid-cols-3 auto-rows-min gap-4  ml-8">
        <div class="col-span-3">
            <div class="flex grid content-center text-white/80 rounded-xl ">
                <div class="font-semibold text-8xl">Paruošti:</div>
            </div>
        </div>
        @for ($i = 16; $i <= 30; $i++)
            <div class="relative text-center shadow-md  hover:drop-shadow-2xl hover:shadow-md  hover:scale-101
                aspect-video overflow-hidden rounded-xl  bg-gray-900/20 bg-yellow-300/20  dark:border-neutral-700">
                <div class="flex grid content-center flex-col gap-2 h-full text-white/80 rounded-xl w-full">
                    <div class="font-semibold text-8xl">{{ $i }}</div>
                </div>
            </div>
        @endfor
    </div>
</div>
