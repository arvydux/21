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
<div x-data="{ koreguoti: window.koreguotiActive, editingProduct: '', editingProductId: null, editingLeft: '', editingAttention: false }" x-init="initSwapSortable($el, $wire, 'updateOrder')" @koreguoti-changed.window="koreguoti = $event.detail.active" class="grid grid-cols-2 @xl:grid-cols-3 {{ $buttonColumns === 3 ? '@3xl:grid-cols-3' : ($buttonColumns === 5 ? '@3xl:grid-cols-5' : '@3xl:grid-cols-4') }} auto-rows-min {{ $buttonGap === 1 ? 'gap-2' : ($buttonGap === 2 ? 'gap-3' : ($buttonGap === 3 ? 'gap-3' : ($buttonGap === 5 ? 'gap-5' : 'gap-4'))) }}">
    @foreach(\App\Models\OtherProduct::where('show', true)->when(! $showAll, fn ($q) => $q->where('subcategory_id', $subcategoryId))->orderBy('position')->get() as $productName)
        <div data-sortable-id="{{ $productName->id }}" wire:key="other-{{ $productName->id }}" class="h-full"
             @click.capture="if (koreguoti) { $event.stopImmediatePropagation(); $event.preventDefault(); if ({{ $productName->attention ? 'true' : 'false' }}) { $wire.toggleAttention({{ $productName->id }}); } else { editingProduct = @js($productName->name); editingProductId = {{ $productName->id }}; editingLeft = ''; editingAttention = false; $flux.modal('koreguoti-edit-other').show(); } }">
            <div wire:click="addProduct(@js($productName->name), '{{ $productName->price }}')" class="h-full">
                <x-kasa.product-card :product="$productName" :price="number_format((float) $productName->price, 2, ',', '').' €'" :button-style="$buttonStyle" :padding="$buttonPadding" />
            </div>
        </div>
    @endforeach

    <flux:modal name="koreguoti-edit-other" class="w-80">
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
                <flux:button variant="primary" x-on:click="$wire.updateLeft(editingProductId, editingLeft !== '' ? parseInt(editingLeft) : null); $flux.modal('koreguoti-edit-other').close(); editingProductId = null; editingLeft = ''">Atnaujinti</flux:button>
                <flux:button x-on:click="$flux.modal('koreguoti-edit-other').close()">Uždaryti</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
