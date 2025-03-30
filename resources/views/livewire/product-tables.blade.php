<div>
    @if(isset($buttonName))
        @if($buttonName === 'ceburekai')
            <livewire:dynamic-component :component="'cebureks-table'" />
        @endif
        @if($buttonName === 'kibinai')
            <livewire:dynamic-component :component="'kibinai-table'" />
        @endif
        @if($buttonName === 'drinks')
            <livewire:dynamic-component :component="'drinks-table'" />
        @endif
        @if($buttonName === 'other')
            <livewire:dynamic-component :component="'other-products-table'" />
        @endif
        @if($buttonName === 'toppings')
            <livewire:dynamic-component :component="'toppings-table'" />
        @endif
   @endif{{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}

</div>
