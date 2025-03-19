<div>
    @if(isset($buttonName))
        @if($buttonName === 'ceburekai')
            <livewire:dynamic-component :component="'ceburekai'" :key="" :mydata="67567567"/>
        @endif
        @if($buttonName === 'kibinai')
            <livewire:dynamic-component :component="'kibinai'" :key="" :mydata="67567567"/>
        @endif
    @endif{{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
</div>
