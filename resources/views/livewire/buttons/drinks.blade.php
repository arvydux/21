@php
    $buttonStyle = cache('pos_button_style', 'compact');
    $buttonColumns = (int) cache('pos_button_columns', 4);
    $buttonGap = (int) cache('pos_button_gap', 4);
    $buttonPadding = (int) cache('pos_button_padding', 3);
    $btnPx = match($buttonPadding) { 1 => 'px-0', 2 => 'px-2', 4 => 'px-4', 5 => 'px-5', default => 'px-3' };
    $btnPy = match($buttonPadding) { 1 => 'py-0', 2 => 'py-2', 4 => 'py-4', 5 => 'py-5', default => 'py-3' };
@endphp
<div wire:poll.5s x-data="{ koreguoti: window.koreguotiActive, editingProduct: '', editingProductId: null, editingLeft: '', editingAttention: false }" x-init="initSwapSortable($el, $wire, 'updateOrder')" @koreguoti-changed.window="koreguoti = $event.detail.active" class="grid {{ $buttonColumns === 3 ? 'md:grid-cols-3' : ($buttonColumns === 5 ? 'md:grid-cols-5' : 'md:grid-cols-4') }} auto-rows-min {{ $buttonGap === 1 ? 'gap-1' : ($buttonGap === 2 ? 'gap-2' : ($buttonGap === 3 ? 'gap-3' : ($buttonGap === 5 ? 'gap-5' : 'gap-4'))) }} mt-10 mt-4">
    @foreach(\App\Models\Drink::where('show', true)->orderBy('position')->get() as $productName)
        <div data-sortable-id="{{ $productName->id }}" wire:key="drink-{{ $productName->id }}"
             @click.capture="if (koreguoti) { $event.stopImmediatePropagation(); $event.preventDefault(); if ({{ $productName->attention ? 'true' : 'false' }}) { $wire.toggleAttention({{ $productName->id }}); } else { editingProduct = '{{ $productName->name }}'; editingProductId = {{ $productName->id }}; editingLeft = ''; editingAttention = false; $flux.modal('koreguoti-edit-drinks').show(); } }">
            <div wire:click="addProduct('{{ $productName->name }}', '{{ $productName->price }}')"
                class="relative {{ $buttonStyle === 'large' ? 'aspect-[5/3]' : 'aspect-[5/2.3]' }} overflow-hidden rounded-2xl cursor-pointer
                    {{ $productName->attention ? 'bg-red-500/70 hover:bg-red-500/70' : 'bg-white/10 hover:bg-white/20' }} backdrop-blur-lg border border-white/25
                    hover:scale-[1.03] hover:-translate-y-0.5
                    active:scale-[0.98]
                    transition-all duration-300"
                style="box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.25);">
                <div class="flex h-full text-white items-center font-nunito">
                    <div class="flex-1 {{ $btnPx }} {{ $btnPy }} text-center">
                        <div class="font-extrabold leading-tight antialiased {{ $productName->left !== null ? ($buttonStyle === 'large' ? 'text-sm' : 'text-xs') : ($buttonStyle === 'large' ? 'text-xl' : 'text-base') }}" style="text-shadow: 0 0 20px rgba(255,255,255,0.15), 0 2px 4px rgba(0,0,0,0.3);">
                            {{ $productName->name }}
                        </div>
                    </div>
                    @if($productName->left !== null)
                    <div class="w-px bg-white/20 self-stretch {{ $buttonStyle === 'large' ? 'my-3' : 'my-4' }} shrink-0"></div>
                    <div class="flex items-baseline justify-center {{ $btnPx }} shrink-0 gap-0.5">
                        <div class="font-black {{ $buttonStyle === 'large' ? 'text-2xl' : 'text-xl' }} leading-none" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                            <span x-show="editingProductId === {{ $productName->id }} && editingLeft !== ''"
                                  x-text="editingLeft"></span>
                            <span x-show="!(editingProductId === {{ $productName->id }} && editingLeft !== '')">{{ $productName->left }}</span>
                        </div>
                        <div class="{{ $buttonStyle === 'large' ? 'text-sm' : 'text-xs' }} font-semibold opacity-75">vnt.</div>
                    </div>
                    @else
                    <div class="w-px bg-white/20 self-stretch {{ $buttonStyle === 'large' ? 'my-3' : 'my-4' }} shrink-0"
                         x-show="editingProductId === {{ $productName->id }} && editingLeft !== ''"></div>
                    <div class="flex items-baseline justify-center px-3 shrink-0 gap-0.5"
                         x-show="editingProductId === {{ $productName->id }} && editingLeft !== ''">
                        <div x-text="editingLeft" class="font-black {{ $buttonStyle === 'large' ? 'text-2xl' : 'text-xl' }} leading-none" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>
                        <div class="{{ $buttonStyle === 'large' ? 'text-sm' : 'text-xs' }} font-semibold opacity-75">vnt.</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    <flux:modal name="koreguoti-edit-drinks" class="w-80">
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
                <flux:button variant="primary" x-on:click="$wire.updateLeft(editingProductId, editingLeft !== '' ? parseInt(editingLeft) : null); $flux.modal('koreguoti-edit-drinks').close()">Atnaujinti</flux:button>
                <flux:button x-on:click="$flux.modal('koreguoti-edit-drinks').close()">Uždaryti</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
