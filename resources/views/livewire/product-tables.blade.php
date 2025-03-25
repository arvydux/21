<div>
    @if(isset($buttonName))
        @if($buttonName === 'ceburekai')
        @endif
        @if($buttonName === 'kibinai')
            <livewire:dynamic-component :component="'kibinai-table'" />@endif
    @endif{{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}

</div>
