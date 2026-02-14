<div class="">
    @if($bakerySum)
        <div class=" flex justify-between items-center">
            <div class="font-extrabold text-md text-white/80 tracking-wide antialiased">Kepiniai</div>
            <div class="font-extrabold text-md text-white/80 tracking-wide antialiased">{{$bakerySum}} €</div>
        </div>
    @endif
    @if($drinksSum)
    <div class="flex justify-between items-center">
        <div class="font-extrabold text-md text-white/80 tracking-wide antialiased">Gėrimai</div>
        <div class="font-extrabold text-md text-white/80 tracking-wide antialiased">{{$drinksSum}} €</div>
    </div>
    @endif
    @if($packageSum)
        <div class="flex justify-between items-center">
            <div class="font-extrabold text-md text-white/80 tracking-wide antialiased">Pakuotės</div>
            <div class="font-extrabold text-md text-white/80 tracking-wide antialiased">{{$packageSum}} €</div>
        </div>
    @endif
</div>
