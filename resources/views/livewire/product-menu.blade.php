<div>
    @if(isset($buttonName))
        @if($buttonName === 'ceburekai')
            <livewire:dynamic-component :component="'ceburekai'"/>
        @endif
        @if($buttonName === 'kibinai')
            <livewire:dynamic-component :component="'kibinai'" />
        @endif
        @if($buttonName === 'drinks')
            <livewire:dynamic-component :component="'drinks'" />
        @endif
        @if($buttonName === 'other')
            <livewire:dynamic-component :component="'other-products'" />
        @endif
    @endif{{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}

</div>
