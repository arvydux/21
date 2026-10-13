<x-layouts.app.header :title="$title ?? null">
    @if (Route::is('orders'))
        {{ $slot }}
    @elseif (Route::is('kitchen'))
        <flux:main class="p-0">
            {{ $slot }}
        </flux:main>
    @else
        <flux:main :class="Arr::toCssClasses(['bg-gradient-to-tr from-lime-600 via-emerald-600 to-teal-800', 'p-0!' => Route::is('kasa')])">
            {{ $slot }}
        </flux:main>
    @endif
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('cart', {
                    items: {},
                    count(name) {
                        let total = 0;
                        for (const [orderName, amount] of Object.entries(this.items)) {
                            if (orderName === name || orderName.startsWith(name + ' (')) {
                                total += amount;
                            }
                        }
                        return total;
                    },
                });

                // Finger scrolling for POS touch monitors that report touches as a mouse:
                // a short tap still clicks, a drag scrolls (with momentum) and swallows the click.
                // Replaces the scrollbar: fades the top/bottom edge while there is more to scroll that way.
                Alpine.directive('scroll-fade', (el, {}, { cleanup }) => {
                    const fade = 32;
                    const update = () => {
                        const top = el.scrollTop > 2;
                        const bottom = el.scrollTop + el.clientHeight < el.scrollHeight - 2;
                        const mask = (top || bottom)
                            ? `linear-gradient(to bottom, ${top ? 'transparent' : 'black'} 0, black ${fade}px, black calc(100% - ${fade}px), ${bottom ? 'transparent' : 'black'} 100%)`
                            : '';
                        el.style.maskImage = el.style.webkitMaskImage = mask;
                        el.toggleAttribute('data-fade-top', top);
                        el.toggleAttribute('data-fade-bottom', bottom);
                    };
                    const resize = new ResizeObserver(update);
                    const mutations = new MutationObserver(update);
                    resize.observe(el);
                    mutations.observe(el, { childList: true, subtree: true });
                    el.addEventListener('scroll', update, { passive: true });
                    update();
                    cleanup(() => {
                        resize.disconnect();
                        mutations.disconnect();
                        el.removeEventListener('scroll', update);
                    });
                });

                Alpine.directive('drag-scroll', (el, { modifiers }, { cleanup }) => {
                    const horizontal = modifiers.includes('x');
                    const threshold = 8;
                    let start = null, dragging = false, suppressClick = false, last = 0, lastTime = 0, velocity = 0, frame = null;

                    const position = (e) => horizontal ? e.clientX : e.clientY;
                    const scrollBy = (delta) => horizontal ? (el.scrollLeft += delta) : (el.scrollTop += delta);
                    const glide = () => {
                        velocity *= 0.95;
                        if (Math.abs(velocity) < 0.05) { frame = null; return; }
                        scrollBy(-velocity * 16);
                        frame = requestAnimationFrame(glide);
                    };

                    const down = (e) => {
                        if (e.pointerType === 'touch' || e.button !== 0) return;
                        if (e.target.closest('dialog, input, textarea, select')) return;
                        cancelAnimationFrame(frame);
                        start = last = position(e);
                        lastTime = e.timeStamp;
                        velocity = 0;
                        dragging = false;
                    };
                    const move = (e) => {
                        if (start === null) return;
                        if (! dragging && Math.abs(position(e) - start) < threshold) return;
                        if (! dragging) {
                            dragging = true;
                            try { el.setPointerCapture(e.pointerId); } catch {}
                            el.dataset.dragging = '';
                        }
                        const delta = position(e) - last;
                        const elapsed = Math.max(e.timeStamp - lastTime, 1);
                        velocity = 0.8 * (delta / elapsed) + 0.2 * velocity;
                        scrollBy(-delta);
                        last = position(e);
                        lastTime = e.timeStamp;
                    };
                    const up = (e) => {
                        if (start === null) return;
                        start = null;
                        if (! dragging) return;
                        dragging = false;
                        suppressClick = true;
                        delete el.dataset.dragging;
                        setTimeout(() => suppressClick = false, 0);
                        if (e.timeStamp - lastTime < 80) frame = requestAnimationFrame(glide);
                    };
                    const click = (e) => {
                        if (! suppressClick) return;
                        e.preventDefault();
                        e.stopPropagation();
                        suppressClick = false;
                    };

                    el.addEventListener('pointerdown', down);
                    el.addEventListener('pointermove', move);
                    el.addEventListener('pointerup', up);
                    el.addEventListener('pointercancel', up);
                    el.addEventListener('click', click, true);
                    el.addEventListener('dragstart', (e) => e.preventDefault());
                    cleanup(() => {
                        cancelAnimationFrame(frame);
                        el.removeEventListener('pointerdown', down);
                        el.removeEventListener('pointermove', move);
                        el.removeEventListener('pointerup', up);
                        el.removeEventListener('pointercancel', up);
                        el.removeEventListener('click', click, true);
                    });
                });
            });
            window.addEventListener('cart-updated', (e) => {
                if (window.Alpine) {
                    Alpine.store('cart').items = e.detail.counts;
                }
            });
        </script>
        @livewireScripts
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
        <style>
            [x-cloak] {
                display: none !important;
            }
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

            window.koreguotiActive = false;
            window.addEventListener('koreguoti-changed', (e) => {
                window.koreguotiActive = e.detail.active;
            });

            document.addEventListener('livewire:initialized', () => {
                Livewire.hook('commit', ({ commit }) => {
                    commit.calls = commit.calls.filter(call => call.method !== 'toJSON');
                });
            });

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
