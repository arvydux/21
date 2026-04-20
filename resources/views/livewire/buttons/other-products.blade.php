<div x-data="{ koreguoti: window.koreguotiActive, editingProduct: '', editingProductId: null, editingLeft: '', editingAttention: false }" x-init="initSwapSortable($el, $wire, 'updateOrder')" @koreguoti-changed.window="koreguoti = $event.detail.active" class="grid md:grid-cols-3 auto-rows-min gap-4 mt-10 mt-4">
    @foreach(\App\Models\OtherProduct::where('show', true)->orderBy('position')->get() as $productName)
        <div data-sortable-id="{{ $productName->id }}" wire:key="other-{{ $productName->id }}"
             @click.capture="if (koreguoti) { $event.stopImmediatePropagation(); $event.preventDefault(); if ({{ $productName->attention ? 'true' : 'false' }}) { $wire.toggleAttention({{ $productName->id }}); } else { editingProduct = '{{ $productName->name }}'; editingProductId = {{ $productName->id }}; editingLeft = ''; editingAttention = false; $flux.modal('koreguoti-edit-other').show(); } }">
            <div wire:click="addProduct('{{ $productName->name }}', '{{ $productName->price }}')"
                class="relative text-center aspect-video overflow-hidden rounded-2xl cursor-pointer
                    {{ $productName->attention ? 'bg-red-500/70 hover:bg-red-500/70' : 'bg-white/10 hover:bg-white/20' }} backdrop-blur-lg border border-white/25
                    hover:scale-[1.03] hover:-translate-y-0.5
                    active:scale-[0.98]
                    transition-all duration-300"
                style="box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.25);">
                <div class="flex grid content-center flex-col gap-2 h-full text-white rounded-2xl w-full">
                    <div class="font-extrabold text-lg tracking-wide antialiased" style="text-shadow: 0 0 20px rgba(255,255,255,0.15), 0 2px 4px rgba(0,0,0,0.3);">
                        <span x-show="editingProductId === {{ $productName->id }} && editingLeft !== ''"
                              x-text="'{{ $productName->name }} - ' + editingLeft"></span>
                        <span x-show="!(editingProductId === {{ $productName->id }} && editingLeft !== '')">{{ $productName->name }}{{ $productName->left !== null ? ' - ' . $productName->left : '' }}</span>
                    </div>
                </div>
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
                <flux:button variant="primary" x-on:click="$wire.updateLeft(editingProductId, editingLeft !== '' ? parseInt(editingLeft) : null); $flux.modal('koreguoti-edit-other').close()">Atnaujinti</flux:button>
                <flux:button x-on:click="$flux.modal('koreguoti-edit-other').close()">Uždaryti</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
