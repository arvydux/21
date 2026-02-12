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
        @livewireScripts
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
        <style>
            .swap-highlight {
                outline: 3px solid rgba(255, 255, 255, 0.6);
                outline-offset: -3px;
                border-radius: 0.75rem;
            }
            .sortable-ghost {
                opacity: 0;
            }
        </style>
        <script>
            if (typeof Sortable !== 'undefined' && Sortable.Swap) {
                Sortable.mount(new Sortable.Swap());
            }

            window.initSwapSortable = function(el, wire, method) {
                if (el._sortableInstance) return;

                el._sortableInstance = new Sortable(el, {
                    swap: true,
                    swapClass: 'swap-highlight',
                    animation: 250,
                    delay: 500,
                    delayOnTouchOnly: false,
                    touchStartThreshold: 5,
                    ghostClass: 'sortable-ghost',
                    draggable: '[data-sortable-id]',
                    onEnd(evt) {
                        if (evt.oldIndex === evt.newIndex) return;
                        let items = [...el.querySelectorAll(':scope > [data-sortable-id]')];
                        let order = items.map((item, index) => ({
                            value: item.dataset.sortableId,
                            order: index + 1
                        }));
                        wire.call(method, order);
                    }
                });
            };
        </script>
</x-layouts.app.header>
