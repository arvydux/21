@php $money = fn (float $amount): string => number_format($amount, 2, ',', ' ').' €'; @endphp
<div class="flex h-full flex-col overflow-hidden" x-data="{ summaryOpen: false }"
     style="background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2);"
     xmlns:flux="http://www.w3.org/1999/html">
    <div class="flex items-start justify-between gap-3 border-b border-white/10 px-5 py-4 font-nunito">
        <div>
            <h2 class="kasa-title">Krepšelis</h2>
            <p class="kasa-meta">{{ $this->itemCountLabel($itemCount) }}</p>
        </div>
        <div id="sound-activation-slot" wire:ignore class="@container flex min-w-0 flex-1 justify-center"></div>
        <div class="flex items-center gap-2">
            <button wire:click="toggleKoreguoti" title="Koreguoti"
                    class="flex size-11 items-center justify-center rounded-xl ring-1 transition-all duration-200 active:scale-[0.95]
                           {{ $koreguoti ? 'bg-red-500 text-white ring-red-300 shadow-lg shadow-red-500/40' : 'bg-white/10 text-white/80 ring-white/15 hover:bg-white/20' }}">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4L18.768 9.232a2.5 2.5 0 00-3.536-3.536L4.464 16.464 4 20z"></path>
                </svg>
            </button>
            <button wire:click="$toggle('byPhone')" title="Telefonu"
                    class="flex size-11 items-center justify-center rounded-xl ring-1 transition-all duration-200 active:scale-[0.95]
                           {{ $byPhone ? 'bg-amber-400 text-emerald-950 ring-amber-200 shadow-lg shadow-amber-500/30' : 'bg-white/10 text-white/80 ring-white/15 hover:bg-white/20' }}">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
            </button>
            <flux:modal.trigger name="see-orders">
                <button title="Užsakymai"
                        class="flex size-11 items-center justify-center rounded-xl bg-white/10 text-white/80 ring-1 ring-white/15 transition-all duration-200 hover:bg-white/20 active:scale-[0.95]">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 7h6m-6 4h6"></path>
                    </svg>
                </button>
            </flux:modal.trigger>
        </div>
    </div>

    @teleport('body')
        <div class="pointer-events-none fixed inset-x-0 top-4 z-[60] flex justify-center px-4 font-nunito">
            @if($printerWarning = $this->printerWarning())
                <div data-printer-warning role="alert"
                     class="pointer-events-auto flex max-w-xl items-center gap-3 rounded-2xl bg-red-600 py-3 pl-4 pr-3 text-white shadow-2xl shadow-black/40 ring-2 ring-red-300">
                    <svg class="size-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span class="flex-1 text-lg font-bold">{{ $printerWarning }}</span>
                    <button wire:click="retryFailedReceipts" wire:loading.attr="disabled" wire:target="retryFailedReceipts"
                            class="shrink-0 rounded-xl bg-white px-3 py-2 font-extrabold text-red-700 transition-all duration-200 hover:bg-red-50 active:scale-[0.97] disabled:opacity-60">
                        <span wire:loading.remove wire:target="retryFailedReceipts">Spausdinti dar kartą</span>
                        <span wire:loading wire:target="retryFailedReceipts">Spausdinama…</span>
                    </button>
                    <button wire:click="dismissPrinterWarning" title="Uždaryti"
                            class="flex size-9 shrink-0 items-center justify-center rounded-xl text-white/80 transition-all duration-200 hover:bg-white/15 active:scale-[0.95]">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif
        </div>
    @endteleport

    @teleport('body')
        <div x-show="summaryOpen" x-cloak
             x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             x-on:click="summaryOpen = false" x-on:keydown.escape.window="summaryOpen = false"
             data-order-summary
             class="fixed inset-0 z-50 flex cursor-pointer items-center justify-center bg-emerald-950/80 p-4 font-nunito backdrop-blur-md sm:p-8">
            <div x-show="summaryOpen"
                 x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="scale-95 translate-y-4" x-transition:enter-end="scale-100 translate-y-0"
                 class="flex max-h-full w-full flex-col overflow-hidden rounded-3xl bg-stone-50 shadow-2xl shadow-black/50 ring-4 ring-white/20
                        {{ count($orders) > 8 ? 'max-w-6xl' : 'max-w-2xl' }}">
                <div class="flex items-center gap-4 bg-linear-to-r from-emerald-700 to-teal-700 px-6 py-5">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-white ring-1 ring-white/25">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6h11M9 12h11M9 18h11M4 6l1 1 2-2M4 12l1 1 2-2M4 18l1 1 2-2"></path>
                        </svg>
                    </div>
                    <h2 class="flex-1 text-2xl font-extrabold tracking-wide text-white">Visas užsakymas</h2>
                    <span class="rounded-full bg-amber-400 px-4 py-1.5 text-base font-extrabold text-emerald-950 shadow-md">{{ $this->itemCountLabel($itemCount) }}</span>
                </div>

                <div x-drag-scroll x-scroll-fade class="no-scrollbar min-h-0 flex-1 touch-pan-y select-none overflow-y-auto overscroll-contain px-6 py-3">
                    @if(collect($orders)->contains(fn ($order) => $order->isForAdults()))
                        <div data-adults-warning class="mb-2 mt-1 flex items-center gap-3 rounded-2xl bg-red-50 px-4 py-3 ring-1 ring-red-200">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-red-600 text-sm font-black text-white shadow-md shadow-red-600/30">18+</span>
                            <p class="font-bold leading-snug text-red-800">Užsakyme yra alkoholio. Patikrinkite, ar pirkėjui yra 18 metų.</p>
                        </div>
                    @endif
                    @forelse($orders as $order)
                        @php $unitPrice = $this->unitPrice($order); @endphp
                        @if($loop->first)
                            <div @class(['gap-x-10', 'lg:columns-2' => count($orders) > 8])>
                        @endif
                        <div @class([
                                'flex break-inside-avoid items-start gap-4 border-b border-dashed py-3.5',
                                'border-stone-300' => ! $order->isForAdults(),
                                '-mx-3 rounded-xl border-red-200 bg-red-50 px-3' => $order->isForAdults(),
                             ]) wire:key="summary-{{ $order->id }}">
                            <span @class([
                                'flex h-9 min-w-11 shrink-0 items-center justify-center rounded-xl px-2 text-lg font-extrabold tabular-nums text-white shadow-sm',
                                'bg-emerald-700' => ! $order->isForAdults(),
                                'bg-red-600' => $order->isForAdults(),
                            ])>{{ $order->amount }}×</span>
                            <div class="min-w-0 flex-1">
                                @foreach(json_decode($order->name) as $name)
                                    @foreach($name as $name => $price)
                                        <p class="text-lg font-bold leading-snug text-stone-900">{{ $name }}@if($order->isForAdults())<span class="ml-1.5 inline-flex -translate-y-px items-center rounded-md bg-red-600 px-1.5 py-0.5 align-middle text-xs font-extrabold leading-none text-white" title="Tik suaugusiems">18+</span>@endif</p>
                                    @endforeach
                                @endforeach
                                @if(isset($order->toppings))
                                    @foreach(json_decode($order->toppings) as $topping)
                                        @foreach($topping as $name => $toppingPrice)
                                            <p class="text-sm font-semibold text-stone-600">+ {{ $name }} <span class="text-stone-400">{{ $money((float) $toppingPrice) }}</span></p>
                                        @endforeach
                                    @endforeach
                                @endif
                                @if($order->package)
                                    <p class="text-sm font-semibold text-stone-600">+ Pakuotė <span class="text-stone-400">{{ $money((float) $order->package) }}</span></p>
                                @endif
                                @if($order->takeaway || $order->comments)
                                    <div class="mt-1.5 flex flex-wrap items-center gap-2">
                                        @if($order->takeaway)
                                            <span class="rounded-md bg-amber-400 px-2 py-0.5 text-xs font-extrabold uppercase tracking-wide text-emerald-950">Išsinešimui</span>
                                        @endif
                                        @if($order->comments)
                                            <span class="text-sm font-semibold italic text-amber-700">„{{ $order->comments }}“</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-lg font-extrabold tabular-nums text-stone-900">{{ $money($unitPrice * $order->amount) }}</p>
                                @if($order->amount > 1)
                                    <p class="text-sm font-semibold tabular-nums text-stone-400">{{ $money($unitPrice) }} / vnt.</p>
                                @endif
                            </div>
                        </div>
                        @if($loop->last)
                            </div>
                        @endif
                    @empty
                        <p class="py-12 text-center text-lg font-semibold text-stone-400">Krepšelis tuščias</p>
                    @endforelse
                </div>

                <div class="flex items-center justify-between gap-4 bg-emerald-900 px-6 py-5">
                    <span class="text-lg font-bold uppercase tracking-wider text-emerald-200">Iš viso</span>
                    <span class="text-4xl font-extrabold tracking-tight text-white tabular-nums">{{ $money($cartTotal) }}</span>
                </div>
            </div>
        </div>
    @endteleport

    <flux:modal name="see-orders" variant="flyout" class="bg-yellow-300">
        <livewire:kasa-orders/>
    </flux:modal>

    <div x-drag-scroll x-scroll-fade data-drag-scroll class="no-scrollbar flex min-h-0 flex-1 touch-pan-y select-none flex-col gap-2 overflow-y-auto overscroll-contain p-3 font-nunito">
        @forelse($orders as $order)
            @php $unitPrice = $this->unitPrice($order); @endphp
            <div @class([
                    'flex flex-col gap-3 rounded-2xl bg-white/10 p-4 ring-1',
                    'ring-white/10' => ! $order->isForAdults(),
                    'ring-red-400/60' => $order->isForAdults(),
                 ]) wire:key="order-{{ $order->id }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        @foreach(json_decode($order->name) as $name)
                            @foreach($name as $name => $price)
                                <h3 class="kasa-item text-white">{{ $name }}@if($order->isForAdults())<span class="ml-1.5 inline-flex -translate-y-px items-center rounded-md bg-red-600 px-1.5 py-0.5 align-middle text-xs font-extrabold leading-none text-white" title="Tik suaugusiems">18+</span>@endif</h3>
                            @endforeach
                        @endforeach

                        @if(isset($order->toppings))
                            @foreach(json_decode($order->toppings) as $topping)
                                @foreach($topping as $name => $toppingPrice)
                                    <p class="kasa-meta text-white/75">+ {{ $name }} <span class="text-white/55">{{ $money((float) $toppingPrice) }}</span></p>
                                @endforeach
                            @endforeach
                        @endif

                        @if($order->package)
                            <p class="kasa-meta text-white/75">+ Pakuotė <span class="text-white/55">{{ $money((float) $order->package) }}</span></p>
                        @endif

                        <p class="kasa-meta mt-0.5">{{ $money($unitPrice) }} / vnt.</p>

                        @if($order->comments)
                            <p class="mt-1 text-sm italic text-amber-200/90">„{{ $order->comments }}“</p>
                        @endif
                    </div>
                    <div class="kasa-item shrink-0 text-right font-extrabold text-white">{{ $money($unitPrice * $order->amount) }}</div>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <button wire:click="changeTakeaway({{ $order->id }})"
                                class="flex h-10 touch-manipulation items-center rounded-lg px-4 text-sm font-bold transition-all duration-200 active:scale-[0.95]
                                       {{ $order->takeaway ? 'bg-amber-400/90 text-emerald-950' : 'bg-emerald-400/20 text-emerald-200 ring-1 ring-emerald-300/30' }}">
                            {{ $order->takeaway ? 'Išsinešimui' : 'Vietoje' }}
                        </button>
                        <flux:modal.trigger wire:click="openCommentForOrder({{ $order->id }})" name="add-comment-for-order-{{ $order->id }}">
                            <button title="Komentaras"
                                    class="flex size-10 touch-manipulation items-center justify-center rounded-lg transition-all duration-200 active:scale-[0.95]
                                           {{ $order->comments ? 'bg-amber-300/20 text-amber-200' : 'text-white/70 hover:bg-white/10' }}">
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8-1.657 0-3.204-.402-4.5-1.1L3 19l1.1-3.5C3.402 14.204 3 12.657 3 11c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </button>
                        </flux:modal.trigger>
                    </div>

                    <div class="flex items-center gap-1 rounded-full bg-black/25 p-1">
                        <button wire:click="removeOneOrder({{ $order->id }})"
                                class="flex size-11 touch-manipulation items-center justify-center rounded-full text-white/80 transition-all duration-200 hover:bg-white/10 active:scale-[0.92]">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </button>
                        <span class="min-w-7 text-center text-lg font-extrabold text-white">{{ $order->amount }}</span>
                        <button wire:click="addOneOrder({{ $order->id }})"
                                class="flex size-11 touch-manipulation items-center justify-center rounded-full bg-white text-emerald-800 shadow-lg shadow-emerald-950/30 transition-all duration-200 hover:bg-emerald-50 active:scale-[0.92]">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12M6 12h12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <flux:modal name="add-comment-for-order-{{ $order->id }}" variant="bare"
                            class="w-full max-w-xl p-4 outline-none focus:outline-none backdrop:bg-emerald-950/80! backdrop:backdrop-blur-md">
                    <form wire:submit.prevent="makeCommentForOrder({{ $order->id }})" x-on:submit="$flux.modals().close()" data-comment-modal
                          class="flex flex-col overflow-hidden rounded-3xl bg-linear-to-br from-emerald-800 to-teal-900 font-nunito text-white shadow-2xl shadow-black/50 ring-1 ring-white/20">
                        <div class="flex items-center gap-3 border-b border-white/10 px-6 py-4">
                            <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-amber-400 text-emerald-950 shadow-md shadow-amber-500/20">
                                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8-1.657 0-3.204-.402-4.5-1.1L3 19l1.1-3.5C3.402 14.204 3 12.657 3 11c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="kasa-meta uppercase tracking-widest">Komentaras</p>
                                <h2 class="kasa-title line-clamp-2">{{ array_key_first(json_decode($order->name, true)[0] ?? []) }}</h2>
                            </div>
                            <button type="button" title="Uždaryti" x-on:click="$flux.modals().close()"
                                    class="flex size-12 shrink-0 touch-manipulation items-center justify-center rounded-xl bg-white/10 text-white/80 ring-1 ring-white/15 transition-all duration-200 hover:bg-white/20 active:scale-[0.95]">
                                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="flex flex-col gap-2.5 px-6 py-5">
                            <label for="comment-for-order-{{ $order->id }}" class="kasa-meta uppercase tracking-widest">Komentarai bei pageidavimai</label>
                            <textarea id="comment-for-order-{{ $order->id }}" wire:model="comments" rows="4" autofocus
                                      placeholder="Pvz.: be svogūnų, daugiau padažo…"
                                      class="min-h-32 w-full resize-none select-text rounded-2xl bg-black/25 p-4 text-lg font-semibold text-white ring-1 ring-white/15 outline-none transition placeholder:font-normal placeholder:text-white/40 focus:ring-2 focus:ring-amber-400"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3 border-t border-white/10 px-6 py-4">
                            <button type="button" x-on:click="$flux.modals().close()"
                                    class="flex h-14 touch-manipulation items-center justify-center rounded-2xl bg-white/10 px-4 text-lg font-bold text-white ring-1 ring-white/15 transition-all duration-200 hover:bg-white/20 active:scale-[0.97]">
                                Atšaukti
                            </button>
                            <button type="submit"
                                    class="flex h-14 touch-manipulation items-center justify-center rounded-2xl bg-white px-4 text-lg font-extrabold text-emerald-800 shadow-lg shadow-emerald-950/30 transition-all duration-200 hover:bg-emerald-50 active:scale-[0.97]">
                                Išsaugoti
                            </button>
                        </div>
                    </form>
                </flux:modal>
            </div>
        @empty
            <div class="flex flex-1 flex-col items-center justify-center gap-2 text-white/50">
                <svg class="size-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="font-semibold">Krepšelis tuščias</span>
            </div>
        @endforelse
    </div>

    <div class="flex flex-col gap-3 border-t border-white/10 p-4 font-nunito">
        @if(count($categorySums) > 0)
            <dl class="flex flex-col gap-1 border-b border-dashed border-white/15 pb-3">
                @foreach($categorySums as $label => $sum)
                    <div class="flex items-baseline justify-between gap-3">
                        <dt class="kasa-meta">{{ $label }}</dt>
                        <dd class="kasa-meta tabular-nums text-white/80">{{ $money($sum) }}</dd>
                    </div>
                @endforeach
            </dl>
        @endif
        <div class="flex items-baseline justify-between">
            <span class="kasa-nav text-white/70">Iš viso</span>
            <span class="text-3xl font-extrabold tracking-tight text-white">{{ $money($cartTotal) }}</span>
        </div>
        <div class="@container flex gap-2">
            <button wire:click="resetOrders" title="Valyti"
                    class="flex h-14 shrink-0 items-center gap-2 rounded-2xl bg-white/10 px-4 font-bold text-white/80 ring-1 ring-white/15 transition-all duration-200 hover:bg-white/20 active:scale-[0.97]">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span class="hidden @xs:inline">Valyti</span>
            </button>
            <button x-on:click="summaryOpen = true" title="Visas užsakymas" data-order-summary-trigger
                    class="relative flex size-14 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-white/85 ring-1 ring-white/15 transition-all duration-200 hover:bg-white/20 active:scale-[0.97]">
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 6h11M9 12h11M9 18h11M4 6l1 1 2-2M4 12l1 1 2-2M4 18l1 1 2-2"></path>
                </svg>
                @if($itemCount > 0)
                    <span class="absolute -right-1.5 -top-1.5 flex h-6 min-w-6 items-center justify-center rounded-full bg-amber-400 px-1.5 text-xs font-extrabold tabular-nums text-emerald-950 shadow-md ring-2 ring-emerald-800">{{ $itemCount }}</span>
                @endif
            </button>
            <button wire:click="makeOrder" wire:loading.attr="disabled" wire:target="makeOrder"
                    class="flex h-14 min-w-0 flex-1 items-center justify-between gap-3 rounded-2xl bg-white px-4 text-lg font-extrabold text-emerald-800 shadow-lg shadow-emerald-950/30 transition-all duration-200 hover:bg-emerald-50 active:scale-[0.97] disabled:opacity-60">
                <span>Užsakyti</span>
                <span class="whitespace-nowrap">{{ $money($cartTotal) }}</span>
            </button>
        </div>
    </div>
</div>
