<div wire:poll.5s class="grid md:grid-cols-2 auto-rows-min gap-4">
    @if(\App\Models\OrderNumbers::count() === 0)
        <video  id="temporaryDiv" style="display: none; z-index: 9999;" autoplay muted  class="absolute top-0 left-0 object-cover w-full h-full -z-10">
            <source src="{{ asset('5.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    @else
    <div class="grid md:grid-cols-3 auto-rows-min gap-4 mr-8">
        <div class="col-span-3">
            <div class="flex grid content-center text-white/80 rounded-xl ">
                <div class="font-semibold text-8xl">Gaminami:</div>
            </div>
        </div>
        @foreach(\App\Models\OrderNumbers::where('is_ready', false)->get() as $order)
            <div class="relative text-center aspect-video overflow-hidden rounded-2xl transition-transform hover:scale-105"
                 style="background: linear-gradient(135deg, #f7971e, #ffd200); box-shadow: 0 8px 32px rgba(255, 210, 0, 0.4);">
                <div class="flex grid content-center flex-col gap-2 h-full rounded-2xl w-full">
                    <div class="font-bold text-8xl text-gray-900">{{ $order->number }}</div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="grid md:grid-cols-3 auto-rows-min gap-4  ml-8">
        <div class="col-span-3">
            <div class="flex grid content-center text-white/80 rounded-xl ">
                <div class="font-semibold text-8xl">Paruošti:</div>
            </div>
        </div>
        @foreach(\App\Models\OrderNumbers::where('is_ready', true)->where('is_taken', false)->orderBy('updated_at', 'desc')->get() as $order)
            <div class="relative text-center aspect-video overflow-hidden rounded-2xl transition-transform hover:scale-105"
                 style="background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.25); box-shadow: 0 8px 32px rgba(48, 207, 208, 0.25);">
                <div class="flex grid content-center flex-col gap-2 h-full rounded-2xl w-full">
                    <div class="font-bold text-8xl text-white">{{ $order->number }}</div>
                </div>
            </div>
        @endforeach
    </div>
    @endif
        <script>
            // Show the div
            const div = document.getElementById('temporaryDiv');
            div.style.display = 'block';

            // Hide the div after 10 seconds (10000 milliseconds)
            setTimeout(() => {
                div.style.display = 'none';
            }, 300000);
        
  
</script>

		
		
</div>
