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
@endphp
<div x-data="{ koreguoti: window.koreguotiActive, chosenName: '', chosenPrice: '', editingProduct: '', editingProductId: null, editingLeft: '', editingAttention: false, editingToppingName: '', editingToppingId: null, editingToppingLeft: '', editingToppingAttention: false }" x-init="initSwapSortable($el, $wire, 'updateOrder')" @koreguoti-changed.window="koreguoti = $event.detail.active" class="grid grid-cols-2 @xl:grid-cols-3 {{ $buttonColumns === 3 ? '@3xl:grid-cols-3' : ($buttonColumns === 5 ? '@3xl:grid-cols-5' : '@3xl:grid-cols-4') }} auto-rows-min {{ $buttonGap === 1 ? 'gap-2' : ($buttonGap === 2 ? 'gap-3' : ($buttonGap === 3 ? 'gap-3' : ($buttonGap === 5 ? 'gap-5' : 'gap-4'))) }}">
    @foreach($products as $product)
        <div data-sortable-id="{{ $product->id }}" wire:key="koldunai-dish-{{ $product->id }}"
             @click.capture="if (koreguoti) { $event.stopImmediatePropagation(); $event.preventDefault(); if ({{ $product->attention ? 'true' : 'false' }}) { $wire.toggleAttention({{ $product->id }}); } else { editingProduct = @js($product->name); editingProductId = {{ $product->id }}; editingLeft = ''; editingAttention = false; $flux.modal('koldunai-koreguoti-edit').show(); } }">
        <flux:modal.trigger name="choose-koldunai-toppings">
            <div wire:click="chooseProduct({{ $product->id }})"
                 x-on:click="chosenName = @js($product->name); chosenPrice = @js(number_format((float) $product->price, 2, ',', '').' €')">
                <x-kasa.product-card :product="$product" :price="number_format((float) $product->price, 2, ',', '').' €'" :button-style="$buttonStyle" :padding="$buttonPadding" />
            </div>
        </flux:modal.trigger>
        </div>
    @endforeach
        <x-kasa.topping-modal name="choose-koldunai-toppings" id-prefix="koldunai"
                             :toppings="$koldunaiToppings"
                             topping-edit-modal="koldunai-koreguoti-topping-edit" />

        <flux:modal name="koldunai-koreguoti-topping-edit" class="w-80">
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
                    <flux:button variant="primary" x-on:click="$wire.updateToppingLeft(editingToppingId, editingToppingLeft !== '' ? parseInt(editingToppingLeft) : null); $flux.modal('koldunai-koreguoti-topping-edit').close(); editingToppingId = null; editingToppingLeft = ''">Atnaujinti</flux:button>
                    <flux:button x-on:click="$flux.modal('koldunai-koreguoti-topping-edit').close()">Uždaryti</flux:button>
                </div>
            </div>
        </flux:modal>

        <flux:modal name="koldunai-koreguoti-edit" class="w-80">
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
                    <flux:button variant="primary" x-on:click="$wire.updateLeft(editingProductId, editingLeft !== '' ? parseInt(editingLeft) : null); $flux.modal('koldunai-koreguoti-edit').close(); editingProductId = null; editingLeft = ''">Atnaujinti</flux:button>
                    <flux:button x-on:click="$flux.modal('koldunai-koreguoti-edit').close()">Uždaryti</flux:button>
                </div>
            </div>
        </flux:modal>
</div>
