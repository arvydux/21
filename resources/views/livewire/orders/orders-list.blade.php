@php $money = fn (float $amount): string => number_format($amount, 2, ',', ' ').' €'; @endphp
<div class="flex h-full flex-col overflow-hidden rounded-3xl"
     style="background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2);"
     xmlns:flux="http://www.w3.org/1999/html">
    <div class="flex items-start justify-between gap-3 border-b border-white/10 px-5 py-4 font-nunito">
        <div>
            <h2 class="text-xl font-extrabold tracking-wide text-white antialiased">Krepšelis</h2>
            <p class="text-sm font-semibold text-white/60">{{ $this->itemCountLabel($itemCount) }}</p>
        </div>
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

    <flux:modal name="see-orders" variant="flyout" class="bg-yellow-300">
        <livewire:kasa-orders/>
    </flux:modal>

    <div class="flex min-h-0 flex-1 flex-col gap-2 overflow-y-auto p-3 font-nunito">
        @forelse($orders as $order)
            @php $unitPrice = $this->unitPrice($order); @endphp
            <div class="flex flex-col gap-3 rounded-2xl bg-white/10 p-4 ring-1 ring-white/10" wire:key="order-{{ $order->id }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        @foreach(json_decode($order->name) as $name)
                            @foreach($name as $name => $price)
                                <h3 class="font-bold leading-snug text-white antialiased">{{ $name }}</h3>
                            @endforeach
                        @endforeach

                        @if(isset($order->toppings))
                            @foreach(json_decode($order->toppings) as $topping)
                                @foreach($topping as $name => $toppingPrice)
                                    <p class="text-sm font-semibold text-white/70">+ {{ $name }} <span class="text-white/50">{{ $money((float) $toppingPrice) }}</span></p>
                                @endforeach
                            @endforeach
                        @endif

                        @if($order->package)
                            <p class="text-sm font-semibold text-white/70">+ Pakuotė <span class="text-white/50">{{ $money((float) $order->package) }}</span></p>
                        @endif

                        <p class="mt-0.5 text-sm text-white/50">{{ $money($unitPrice) }} / vnt.</p>

                        @if($order->comments)
                            <p class="mt-1 text-sm italic text-amber-200/90">„{{ $order->comments }}“</p>
                        @endif
                    </div>
                    <div class="shrink-0 text-right font-extrabold text-white">{{ $money($unitPrice * $order->amount) }}</div>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <button wire:click="changeTakeaway({{ $order->id }})"
                                class="rounded-lg px-3 py-1.5 text-sm font-bold transition-all duration-200 active:scale-[0.95]
                                       {{ $order->takeaway ? 'bg-amber-400/90 text-emerald-950' : 'bg-emerald-400/20 text-emerald-200 ring-1 ring-emerald-300/30' }}">
                            {{ $order->takeaway ? 'Išsinešimui' : 'Vietoje' }}
                        </button>
                        <flux:modal.trigger wire:click="openCommentForOrder({{ $order->id }})" name="add-comment-for-order-{{ $order->id }}">
                            <button title="Komentaras"
                                    class="flex size-9 items-center justify-center rounded-lg transition-all duration-200 active:scale-[0.95]
                                           {{ $order->comments ? 'bg-amber-300/20 text-amber-200' : 'text-white/70 hover:bg-white/10' }}">
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8-1.657 0-3.204-.402-4.5-1.1L3 19l1.1-3.5C3.402 14.204 3 12.657 3 11c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </button>
                        </flux:modal.trigger>
                    </div>

                    <div class="flex items-center gap-1 rounded-full bg-black/25 p-1">
                        <button wire:click="removeOneOrder({{ $order->id }})"
                                class="flex size-10 items-center justify-center rounded-full text-white/80 transition-all duration-200 hover:bg-white/10 active:scale-[0.92]">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </button>
                        <span class="min-w-7 text-center text-lg font-extrabold text-white">{{ $order->amount }}</span>
                        <button wire:click="addOneOrder({{ $order->id }})"
                                class="flex size-10 items-center justify-center rounded-full bg-white text-emerald-800 shadow-lg shadow-emerald-950/30 transition-all duration-200 hover:bg-emerald-50 active:scale-[0.92]">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v12M6 12h12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <flux:modal name="add-comment-for-order-{{ $order->id }}" class="w-full">
                    <div class="space-y-6">
                        <div>
                            @foreach(json_decode($order->name, true) as $name)
                                @foreach($name as $key => $value)
                                @endforeach
                            @endforeach
                            <flux:heading size="lg">{{ $key }}</flux:heading>
                        </div>

                        <flux:textarea label="Komentarai bei pageidavimai." wire:model="comments" />

                        <div class="flex">
                            <flux:spacer />

                            <flux:button wire:click="makeCommentForOrder({{ $order->id }})" x-on:click="$flux.modals().close()"
                                         type="submit" variant="primary">Pridėti komentarus</flux:button>
                        </div>
                    </div>
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
            <dl class="flex flex-col gap-1 border-b border-dashed border-white/15 pb-3 text-sm">
                @foreach($categorySums as $label => $sum)
                    <div class="flex items-baseline justify-between gap-3">
                        <dt class="font-semibold text-white/55">{{ $label }}</dt>
                        <dd class="font-bold tabular-nums text-white/80">{{ $money($sum) }}</dd>
                    </div>
                @endforeach
            </dl>
        @endif
        <div class="flex items-baseline justify-between">
            <span class="font-semibold text-white/60">Iš viso</span>
            <span class="text-3xl font-extrabold tracking-tight text-white">{{ $money($cartTotal) }}</span>
        </div>
        <div class="flex gap-2">
            <button wire:click="resetOrders"
                    class="flex h-14 shrink-0 items-center gap-2 rounded-2xl bg-white/10 px-4 font-bold text-white/80 ring-1 ring-white/15 transition-all duration-200 hover:bg-white/20 active:scale-[0.97]">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Valyti
            </button>
            <button wire:click="makeOrder" wire:loading.attr="disabled" wire:target="makeOrder"
                    class="flex h-14 flex-1 items-center justify-between gap-3 rounded-2xl bg-white px-5 text-lg font-extrabold text-emerald-800 shadow-lg shadow-emerald-950/30 transition-all duration-200 hover:bg-emerald-50 active:scale-[0.97] disabled:opacity-60">
                <span>Užsakyti</span>
                <span class="whitespace-nowrap">{{ $money($cartTotal) }}</span>
            </button>
        </div>
    </div>
</div>
