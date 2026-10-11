<div>
    @if(isset($buttonName))
        @if($buttonName === 'ceburekai')
            <livewire:dynamic-component :component="'menu.tables.cebureks-table'" />
        @endif
        @if($buttonName === 'kepiniai')
            <livewire:dynamic-component :component="'menu.tables.kepiniai-table'" />
        @endif
        @if($buttonName === 'drinks')
            <livewire:dynamic-component :component="'menu.tables.drinks-table'" />
        @endif
        @if($buttonName === 'other')
            <livewire:dynamic-component :component="'menu.tables.other-products-table'" />
        @endif
        @if($buttonName === 'bulviniai')
            <livewire:dynamic-component :component="'menu.tables.potato-dishes-table'" />
        @endif
        @if($buttonName === 'koldunai')
            <livewire:dynamic-component :component="'menu.tables.koldunai-dishes-table'" />
        @endif
        @if($buttonName === 'alkoholis')
            <livewire:dynamic-component :component="'menu.tables.alcoholic-drinks-table'" />
        @endif
        @if($buttonName === 'toppings')
            <livewire:dynamic-component :component="'menu.tables.toppings-table'" />
        @endif
        @if($buttonName === 'potato-toppings')
            <livewire:dynamic-component :component="'menu.tables.potato-toppings-table'" />
        @endif
        @if($buttonName === 'koldunai-toppings')
            <livewire:dynamic-component :component="'menu.tables.koldunai-toppings-table'" />
        @endif
   @endif{{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}

</div>
