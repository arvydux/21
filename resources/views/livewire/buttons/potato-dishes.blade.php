@php
    $__btnSettings = \Illuminate\Support\Facades\Cache::many([
        'pos_button_style' => 'compact',
        'pos_button_columns' => 4,
        'pos_button_gap' => 4,
        'pos_button_padding' => 3,
    ]);
    $buttonStyle = $__btnSettings['pos_button_style'];
    $buttonColumns = (int) $__btnSettings['pos_button_columns'];
    $buttonGap = (int) $__btnSettings['pos_button_gap'];
    $buttonPadding = (int) $__btnSettings['pos_button_padding'];
    $btnPx = match($buttonPadding) { 1 => 'px-0', 2 => 'px-2', 4 => 'px-4', 5 => 'px-5', default => 'px-3' };
    $btnPy = match($buttonPadding) { 1 => 'py-0', 2 => 'py-2', 4 => 'py-4', 5 => 'py-5', default => 'py-3' };
@endphp
<div x-data="{ koreguoti: window.koreguotiActive, editingProduct: '', editingProductId: null, editingLeft: '', editingAttention: false, editingToppingName: '', editingToppingId: null, editingToppingLeft: '', editingToppingAttention: false }" x-init="initSwapSortable($el, $wire, 'updateOrder')" @koreguoti-changed.window="koreguoti = $event.detail.active" class="grid {{ $buttonColumns === 3 ? 'md:grid-cols-3' : ($buttonColumns === 5 ? 'md:grid-cols-5' : 'md:grid-cols-4') }} auto-rows-min {{ $buttonGap === 1 ? 'gap-1' : ($buttonGap === 2 ? 'gap-2' : ($buttonGap === 3 ? 'gap-3' : ($buttonGap === 5 ? 'gap-5' : 'gap-4'))) }} mt-4">
    @foreach($products as $product)
        <div data-sortable-id="{{ $product->id }}" wire:key="potato-dish-{{ $product->id }}"
             @click.capture="if (koreguoti) { $event.stopImmediatePropagation(); $event.preventDefault(); if ({{ $product->attention ? 'true' : 'false' }}) { $wire.toggleAttention({{ $product->id }}); } else { editingProduct = @js($product->name); editingProductId = {{ $product->id }}; editingLeft = ''; editingAttention = false; $flux.modal('potato-koreguoti-edit').show(); } }">
        <flux:modal.trigger name="choose-potato-toppings">
            <div wire:click="chooseProduct({{ $product->id }})" class="relative {{ $buttonStyle === 'large' ? 'aspect-[5/3]' : 'aspect-[5/2.3]' }} overflow-hidden rounded-2xl cursor-pointer
                {{ $product->attention ? 'bg-red-500/70 hover:bg-red-500/70' : ($product->show ? 'bg-white/10 hover:bg-white/20' : 'bg-red-400 hover:bg-red-400') }} backdrop-blur-lg border border-white/25
                hover:scale-[1.03] hover:-translate-y-0.5 active:scale-[0.98] transition-all duration-300"
                style="box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.25);">
                <div class="flex h-full text-white items-center font-nunito">
                    <div class="flex-1 {{ $btnPx }} {{ $btnPy }} text-center">
                        @if($product->number !== null)
                            <div class="mx-auto mb-1 flex size-6 items-center justify-center rounded-full bg-white/90 text-xs font-black leading-none text-emerald-900 shadow">{{ $product->number }}</div>
                        @endif
                        <div class="font-extrabold leading-tight antialiased {{ $product->number !== null ? 'flex items-center justify-center '.($buttonStyle === 'large' ? 'min-h-[3.125rem]' : 'min-h-10') : '' }} {{ $product->left !== null ? ($buttonStyle === 'large' ? 'text-sm' : 'text-xs') : ($buttonStyle === 'large' ? 'text-xl' : 'text-base') }}" style="text-shadow: 0 0 20px rgba(255,255,255,0.15), 0 2px 4px rgba(0,0,0,0.3);">
                            {{ $product->name }}
                        </div>
                    </div>
                    @if($product->left !== null)
                    <div class="w-px bg-white/20 self-stretch {{ $buttonStyle === 'large' ? 'my-3' : 'my-4' }} shrink-0"></div>
                    <div class="flex items-baseline justify-center {{ $btnPx }} shrink-0 gap-0.5">
                        <div class="font-black {{ $buttonStyle === 'large' ? 'text-2xl' : 'text-xl' }} leading-none" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                            <span x-show="editingProductId === {{ $product->id }} && editingLeft !== ''"
                                  x-text="editingLeft"></span>
                            <span x-show="!(editingProductId === {{ $product->id }} && editingLeft !== '')">{{ $product->left }}</span>
                        </div>
                        <div class="{{ $buttonStyle === 'large' ? 'text-sm' : 'text-xs' }} font-semibold opacity-75">vnt.</div>
                    </div>
                    @else
                    <div class="w-px bg-white/20 self-stretch {{ $buttonStyle === 'large' ? 'my-3' : 'my-4' }} shrink-0"
                         x-show="editingProductId === {{ $product->id }} && editingLeft !== ''"></div>
                    <div class="flex items-baseline justify-center px-3 shrink-0 gap-0.5"
                         x-show="editingProductId === {{ $product->id }} && editingLeft !== ''">
                        <div x-text="editingLeft" class="font-black {{ $buttonStyle === 'large' ? 'text-2xl' : 'text-xl' }} leading-none" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>
                        <div class="{{ $buttonStyle === 'large' ? 'text-sm' : 'text-xs' }} font-semibold opacity-75">vnt.</div>
                    </div>
                    @endif
                </div>
            </div>
        </flux:modal.trigger>
        </div>
    @endforeach
        <flux:modal name="choose-potato-toppings" class="w-full max-w-2xl bg-white! rounded-xl shadow-2xl blur-none relative">
            <button type="button" x-on:click="koreguoti = !koreguoti; window.koreguotiActive = koreguoti; window.dispatchEvent(new CustomEvent('koreguoti-changed', { detail: { active: koreguoti } }))"
                    :class="koreguoti
                        ? 'bg-red-500 border-red-500 text-white shadow-md shadow-red-200 hover:bg-red-600 hover:border-red-600'
                        : 'bg-white border-gray-200 text-slate-500 shadow-sm hover:shadow-md hover:border-gray-300'"
                    class="absolute top-4 right-16 inline-flex items-center justify-center py-1.5 px-4 border rounded-xl cursor-pointer transition-all duration-200 text-sm font-bold z-10 outline-none focus:outline-none">
                Koreguoti
            </button>
            <form wire:submit.prevent="addOrder">
                <div class="mx-3 mt-8 mb-3.5">
                    <h3 class="mb-2.5 text-xs font-bold text-center text-slate-500 tracking-widest uppercase">{{ $selectedProduct?->name ?? 'Porcija' }}</h3>
                    <ul class="grid w-full gap-2.5 md:grid-cols-2">
                        @foreach([true => 'Pusė porcijos', false => 'Pilna porcija'] as $isHalf => $portionLabel)
                            <li>
                                <button type="button" wire:click="$set('isHalfPortion', {{ $isHalf ? 'true' : 'false' }})"
                                        class="inline-flex flex-col items-center justify-center w-full py-3 px-4 border rounded-xl cursor-pointer transition-all duration-200 shadow-sm hover:shadow-md
                                        {{ $isHalfPortion === (bool) $isHalf ? 'bg-emerald-50 border-emerald-500 text-emerald-700 shadow-md' : 'bg-white border-gray-200 text-slate-500 hover:border-gray-300' }}">
                                    <span class="text-base font-bold">{{ $portionLabel }}</span>
                                    @if($selectedProduct)
                                        <span class="text-sm font-semibold">{{ number_format($selectedProduct->priceFor((bool) $isHalf), 2, ',', '') }} €</span>
                                    @endif
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-linear-to-b from-slate-50 to-slate-100 rounded-xl mx-3 p-3.5">
                    <h3 class="text-xs mb-3 font-bold text-center text-slate-500 tracking-widest uppercase">Pasirinkti priedus</h3>
                    <ul x-init="initSwapSortable($el, $wire, 'updateToppingOrder')" class="grid w-full gap-2 md:grid-cols-5">
                        @foreach($potatoToppings as $topping)
                            <li data-sortable-id="{{ $topping->id }}" wire:key="potato-topping-{{ $topping->id }}"
                                data-attention="{{ $topping->attention ? '1' : '0' }}"
                                @click.capture="if (koreguoti) { $event.stopImmediatePropagation(); $event.preventDefault(); if ($el.dataset.attention === '1') { $wire.toggleToppingAttention({{ $topping->id }}); } else { editingToppingName = @js($topping->name); editingToppingId = {{ $topping->id }}; editingToppingLeft = ''; editingToppingAttention = false; $flux.modal('potato-koreguoti-topping-edit').show(); } }">
                                <input type="checkbox" id="potato-topping-{{ $topping->id }}" value="{{ $topping->name }}" wire:model.live="toppings"
                                       class="hidden peer">
                                <label for="potato-topping-{{ $topping->id }}"
                                       class="flex items-center aspect-square w-full overflow-hidden bg-linear-to-br text-white/90 rounded-xl cursor-pointer transition-all duration-200 shadow-md hover:shadow-lg hover:-translate-y-0.5 peer-checked:from-emerald-500 peer-checked:to-teal-600 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-emerald-500/30 peer-checked:-translate-y-0.5 peer-checked:ring-2 peer-checked:ring-emerald-400/50 peer-checked:ring-offset-1 peer-checked:hover:from-emerald-500 peer-checked:hover:to-teal-600 font-bold leading-tight {{ $topping->attention ? 'from-red-500 to-red-700 hover:from-red-400 hover:to-red-600' : 'from-slate-600 to-slate-800 hover:from-slate-500 hover:to-slate-700' }}">
                                    <div class="flex-1 p-1 text-center">
                                        <div class="font-extrabold leading-tight antialiased text-sm" style="text-shadow: 0 0 20px rgba(255,255,255,0.15), 0 2px 4px rgba(0,0,0,0.3);">{{ $topping->name }}</div>
                                    </div>
                                    @if($topping->left !== null)
                                    <div class="w-px bg-white/20 self-stretch my-3 shrink-0"></div>
                                    <div class="flex items-baseline justify-center px-1.5 shrink-0 gap-0.5">
                                        <div class="font-black text-base leading-none" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                            <span x-show="editingToppingId === {{ $topping->id }} && editingToppingLeft !== ''" x-text="editingToppingLeft"></span>
                                            <span x-show="!(editingToppingId === {{ $topping->id }} && editingToppingLeft !== '')">{{ $topping->left }}</span>
                                        </div>
                                        <div class="text-[10px] font-semibold opacity-75">vnt.</div>
                                    </div>
                                    @else
                                    <div class="w-px bg-white/20 self-stretch my-3 shrink-0"
                                         x-show="editingToppingId === {{ $topping->id }} && editingToppingLeft !== ''"></div>
                                    <div class="flex items-baseline justify-center px-1.5 shrink-0 gap-0.5"
                                         x-show="editingToppingId === {{ $topping->id }} && editingToppingLeft !== ''">
                                        <div x-text="editingToppingLeft" class="font-black text-base leading-none" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>
                                        <div class="text-[10px] font-semibold opacity-75">vnt.</div>
                                    </div>
                                    @endif
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>

            <div class="mx-3 mt-3.5">
                <h3 class="mb-2.5 text-xs font-bold text-center text-slate-500 tracking-widest uppercase">Kur valgys?</h3>
                <ul class="grid w-full gap-2.5 md:grid-cols-2">
                    <li>
                        <input type="radio" id="potato-hosting-big" name="potato-hosting" wire:model="takeaway" value="0" class="hidden peer">
                        <label for="potato-hosting-big" class="inline-flex items-center justify-center w-full py-3 px-4 text-slate-500 bg-white border border-gray-200 rounded-xl cursor-pointer transition-all duration-200 shadow-sm hover:shadow-md hover:border-gray-300 peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 peer-checked:shadow-md">
                            <svg class="w-5 h-5 mr-2 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.15c0 .415.336.75.75.75z" />
                            </svg>
                            <div class="text-base font-bold">Vietoje</div>
                        </label>
                    </li>
                    <li>
                        <input type="radio" id="potato-hosting-small" name="potato-hosting" wire:model="takeaway" value="1" class="hidden peer" />
                        <label for="potato-hosting-small" class="inline-flex items-center justify-center w-full py-3 px-4 text-slate-500 bg-white border border-gray-200 rounded-xl cursor-pointer transition-all duration-200 shadow-sm hover:shadow-md hover:border-gray-300 peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 peer-checked:shadow-md">
                            <svg class="w-5 h-5 mr-2 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <div class="text-base font-bold">Išsinešimui</div>
                        </label>
                    </li>
                </ul>
            </div>

                <div class="flex justify-center w-full px-3 pt-4 pb-1.5 gap-2.5">
                    <button type="submit" x-on:click="$flux.modals().close()"
                            class="rounded-xl cursor-pointer flex-1 bg-linear-to-r from-emerald-600 to-teal-600 py-2.5 text-white inline-flex items-center justify-center shadow-lg shadow-emerald-500/20 hover:from-emerald-700 hover:to-teal-700 hover:shadow-xl focus:ring-2 focus:outline-none focus:ring-emerald-300 transition-all duration-200 active:scale-[0.98]">
                        <div class="flex-1 text-base font-bold leading-tight text-center">
                            Pridėti ir uždaryti
                        </div>
                    </button>

                    <button type="submit"
                            class="rounded-xl cursor-pointer flex-1 bg-linear-to-r from-emerald-600 to-teal-600 py-2.5 text-white inline-flex items-center justify-center shadow-lg shadow-emerald-500/20 hover:from-emerald-700 hover:to-teal-700 hover:shadow-xl focus:ring-2 focus:outline-none focus:ring-emerald-300 transition-all duration-200 active:scale-[0.98]">
                        <div class="flex-1 text-base font-bold leading-tight text-center">
                            Pridėti dar vieną
                        </div>
                    </button>
                </div>

            </form>
        </flux:modal>

        <flux:modal name="potato-koreguoti-topping-edit" class="w-80">
            <div class="space-y-6">
                <flux:heading size="lg" x-text="editingToppingName"></flux:heading>

                <flux:field>
                    <flux:label>Liko</flux:label>
                    <flux:input type="number" min="0" x-model="editingToppingLeft" />
                </flux:field>

                <button type="button"
                    x-on:click="editingToppingAttention = !editingToppingAttention; $wire.toggleToppingAttention(editingToppingId, null)"
                    :class="editingToppingAttention ? 'bg-red-500 text-white border-red-500 hover:bg-red-600' : 'bg-white text-red-500 border-red-300 hover:bg-red-50'"
                    class="w-full px-4 py-2 rounded-lg text-sm font-semibold border transition-all duration-200">
                    Pažymėti raudonai
                </button>

                <div class="flex justify-end gap-2">
                    <flux:button variant="primary" x-on:click="$wire.updateToppingLeft(editingToppingId, editingToppingLeft !== '' ? parseInt(editingToppingLeft) : null); $flux.modal('potato-koreguoti-topping-edit').close(); editingToppingId = null; editingToppingLeft = ''">Atnaujinti</flux:button>
                    <flux:button x-on:click="$flux.modal('potato-koreguoti-topping-edit').close()">Uždaryti</flux:button>
                </div>
            </div>
        </flux:modal>

        <flux:modal name="potato-koreguoti-edit" class="w-80">
            <div class="space-y-6">
                <flux:heading size="lg" x-text="editingProduct"></flux:heading>

                <flux:field>
                    <flux:label>Liko</flux:label>
                    <flux:input type="number" min="0" x-model="editingLeft" />
                </flux:field>

                <button type="button"
                    x-on:click="editingAttention = !editingAttention; $wire.toggleAttention(editingProductId, null)"
                    :class="editingAttention ? 'bg-red-500 text-white border-red-500 hover:bg-red-600' : 'bg-white text-red-500 border-red-300 hover:bg-red-50'"
                    class="w-full px-4 py-2 rounded-lg text-sm font-semibold border transition-all duration-200">
                    Pažymėti raudonai
                </button>

                <div class="flex justify-end gap-2">
                    <flux:button variant="primary" x-on:click="$wire.updateLeft(editingProductId, editingLeft !== '' ? parseInt(editingLeft) : null); $flux.modal('potato-koreguoti-edit').close(); editingProductId = null; editingLeft = ''">Atnaujinti</flux:button>
                    <flux:button x-on:click="$flux.modal('potato-koreguoti-edit').close()">Uždaryti</flux:button>
                </div>
            </div>
        </flux:modal>
</div>
