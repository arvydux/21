<div class="">
    @if($bakerySum)
        <div class=" flex justify-between items-center">
            <div class="font-semibold text-md  text-[#191919]">Kepiniai</div>
            <div class="font-semibold text-md text-[#191919]">{{$bakerySum}} €</div>
        </div>
    @endif
    @if($drinksSum)
    <div class="flex justify-between items-center">
        <div class="font-semibold text-md  text-[#191919]">Gėrimai</div>
        <div class="font-semibold text-md text-[#191919]">{{$drinksSum}} €</div>
    </div>
    @endif
    @if($packageSum)
        <div class="flex justify-between items-center">
            <div class="font-semibold text-md  text-[#191919]">Pakuotės</div>
            <div class="font-semibold text-md text-[#191919]">{{$packageSum}} €</div>
        </div>
    @endif
</div>
