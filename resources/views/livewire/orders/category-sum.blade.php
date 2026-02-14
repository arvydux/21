<div class="">
    @if($bakerySum)
        <div class=" flex justify-between items-center">
            <div class="font-semibold text-md  text-white/80">Kepiniai</div>
            <div class="font-semibold text-md text-white/80">{{$bakerySum}} €</div>
        </div>
    @endif
    @if($drinksSum)
    <div class="flex justify-between items-center">
        <div class="font-semibold text-md  text-white/80">Gėrimai</div>
        <div class="font-semibold text-md text-white/80">{{$drinksSum}} €</div>
    </div>
    @endif
    @if($packageSum)
        <div class="flex justify-between items-center">
            <div class="font-semibold text-md  text-white/80">Pakuotės</div>
            <div class="font-semibold text-md text-white/80">{{$packageSum}} €</div>
        </div>
    @endif
</div>
