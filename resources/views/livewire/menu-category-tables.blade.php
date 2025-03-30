<div>
    @if(isset($buttonName))
        @if($buttonName === 'ceburekai')
            <livewire:dynamic-component :component="'menu.tables.cebureks-table'" />
        @endif
        @if($buttonName === 'kibinai')
            <livewire:dynamic-component :component="'menu.tables.kibinai-table'" />
        @endif
        @if($buttonName === 'drinks')
            <livewire:dynamic-component :component="'menu.tables.drinks-table'" />
        @endif
        @if($buttonName === 'other')
            <livewire:dynamic-component :component="'menu.tables.other-products-table'" />
        @endif
        @if($buttonName === 'toppings')
            <livewire:dynamic-component :component="'menu.tables.toppings-table'" />
        @endif
   @endif{{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}

</div>
