@php
    $panelStyle = 'background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2);';
    $panelHeight = 'sticky top-0 h-dvh';
    $limits = [
        'menu' => ['min' => \App\Livewire\KasaPanels::MENU_MIN, 'max' => \App\Livewire\KasaPanels::MENU_MAX, 'default' => \App\Livewire\KasaPanels::MENU_DEFAULT],
        'cart' => ['min' => \App\Livewire\KasaPanels::CART_MIN, 'max' => \App\Livewire\KasaPanels::CART_MAX, 'default' => \App\Livewire\KasaPanels::CART_DEFAULT],
        'productsMin' => 400,
        'handles' => 32,
    ];
@endphp
<div x-data="{
        widths: { menu: {{ $menuWidth }}, cart: {{ $cartWidth }} },
        limits: @js($limits),
        dragging: null,
        startX: 0,
        startWidth: 0,
        lastTap: { side: null, at: 0 },
        clamp(side, width) {
            const other = side === 'menu' ? 'cart' : 'menu';
            const room = this.$root.clientWidth - this.widths[other] - this.limits.handles - this.limits.productsMin;
            const max = Math.min(this.limits[side].max, Math.max(this.limits[side].min, room));
            return Math.round(Math.max(this.limits[side].min, Math.min(max, width)));
        },
        start(side, event) {
            const now = Date.now();
            if (this.lastTap.side === side && now - this.lastTap.at < 350) {
                this.lastTap = { side: null, at: 0 };
                this.widths[side] = this.clamp(side, this.limits[side].default);
                this.save();
                return;
            }
            this.lastTap = { side, at: now };
            this.dragging = side;
            this.startX = event.clientX;
            this.startWidth = this.widths[side];
            event.currentTarget.setPointerCapture(event.pointerId);
        },
        move(event) {
            if (! this.dragging) {
                return;
            }
            const delta = event.clientX - this.startX;
            this.widths[this.dragging] = this.clamp(this.dragging, this.startWidth + (this.dragging === 'menu' ? delta : -delta));
        },
        stop() {
            if (! this.dragging) {
                return;
            }
            this.dragging = null;
            this.save();
        },
        save() {
            $wire.saveWidths(this.widths.menu, this.widths.cart);
        },
     }"
     class="flex w-full flex-1 items-start"
     x-bind:class="dragging && 'cursor-col-resize select-none'">
    <aside class="{{ $panelHeight }} flex shrink-0 flex-col overflow-hidden"
           style="width: {{ $menuWidth }}px; {{ $panelStyle }}"
           x-bind:style="{ width: widths.menu + 'px' }">
        <div class="flex items-center gap-3 border-b border-white/10 px-4 py-4 font-nunito">
            <button onclick="window.location='{{ route('home') }}'" title="Atgal"
                    class="flex size-11 shrink-0 touch-manipulation items-center justify-center rounded-xl bg-white/10 text-white/80 ring-1 ring-white/15 transition-all duration-100 hover:bg-white/20 active:scale-[0.95]">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7M3 12h18"></path>
                </svg>
            </button>
            <h2 class="kasa-title truncate">Meniu</h2>
        </div>
        <div class="min-h-0 flex-1 p-3">
            <livewire:kasa-menu/>
        </div>
    </aside>

    <x-kasa.panel-handle side="menu" :class="$panelHeight" />

    <section class="{{ $panelHeight }} flex min-w-0 flex-1 flex-col overflow-hidden" style="{{ $panelStyle }}">
        <div class="min-h-0 flex-1">
            <livewire:kasa-category-section/>
        </div>
        <livewire:ready-orders/>
    </section>

    <x-kasa.panel-handle side="cart" :class="$panelHeight" />

    <div class="{{ $panelHeight }} shrink-0"
         style="width: {{ $cartWidth }}px;"
         x-bind:style="{ width: widths.cart + 'px' }">
        <livewire:orders.orders-list/>
    </div>
</div>
