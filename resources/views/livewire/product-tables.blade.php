<div>
    @if(isset($buttonName))
        @if($buttonName === 'ceburekai')
            <livewire:dynamic-component :component="'cebureks-table'" />
        @endif
        @if($buttonName === 'kibinai')
            <livewire:dynamic-component :component="'kibinai-table'" />
        @endif
            @if($buttonName === 'gerimai')
                <livewire:dynamic-component :component="'drinks-table'" />
            @endif
   @endif{{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}

</div>
