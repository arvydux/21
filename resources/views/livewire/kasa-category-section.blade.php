<div>
    @if(isset($buttonName))
        @if($buttonName === 'ceburekai')
            <livewire:dynamic-component :component="'buttons.ceburekai'"/>
        @endif
        @if($buttonName === 'kibinai')
            <livewire:dynamic-component :component="'buttons.kibinai'" />
        @endif
        @if($buttonName === 'drinks')
            <livewire:dynamic-component :component="'buttons.drinks'" />
        @endif
        @if($buttonName === 'other')
            <livewire:dynamic-component :component="'buttons.other-products'" />
        @endif
    @endif{{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}

</div>
