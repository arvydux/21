<div wire:poll.5s class="relative">
    <!-- Modern gradient background -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 -z-10"></div>

    <!-- Container with improved padding and layout -->
    <div class="min-h-screen px-8 py-12">
        <div class="grid lg:grid-cols-2 gap-12 max-w-7xl mx-auto">

            <!-- Gaminami Section -->
            <div class="space-y-6">
                <!-- Header with modern styling -->
                <div class="flex items-center gap-3 mb-8">
                    <div class="h-1 w-12 bg-gradient-to-r from-amber-400 to-amber-500 rounded-full"></div>
                    <h2 class="text-5xl font-bold text-white tracking-tight">Gaminami</h2>
                </div>

                <!-- Orders Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @foreach(\App\Models\OrderNumbers::where('is_ready', false)->get() as $order)
                        <div
                            wire:click="makeOrderReady('{{ $order->number }}')"
                            class="group relative cursor-pointer"
                        >
                            <!-- Card with gradient and hover effects -->
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-amber-400 to-amber-500 rounded-2xl blur opacity-0 group-hover:opacity-100 transition duration-500"></div>

                            <div class="relative bg-gradient-to-br from-amber-400 to-amber-500 rounded-2xl p-8 h-40 flex flex-col justify-center items-center shadow-2xl transform transition-all duration-300 group-hover:scale-105 group-hover:shadow-amber-500/50"
                                 style="{{ $order->by_phone ? 'background: linear-gradient(135deg, #fff 0%, #f3f4f6 100%);' : '' }}">
                                <div class="text-center">
                                    <div class="text-7xl font-bold {{ $order->by_phone ? 'text-gray-900' : 'text-gray-900' }}">
                                        {{ $order->number }}
                                    </div>
                                    <div class="text-2xl font-semibold {{ $order->by_phone ? 'text-gray-600' : 'text-amber-900' }} mt-2">
                                        {{ (int)$order->minutes_since_created }} min
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Paruošti Section -->
            <div class="space-y-6">
                <!-- Header with modern styling -->
                <div class="flex items-center gap-3 mb-8">
                    <div class="h-1 w-12 bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full"></div>
                    <h2 class="text-5xl font-bold text-white tracking-tight">Paruošti</h2>
                </div>

                <!-- Orders Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @foreach(\App\Models\OrderNumbers::where('is_ready', true)->where('is_taken', false)->orderBy('updated_at', 'desc')->get() as $order)
                        <div
                            wire:click="makeOrderNotReady('{{ $order->number }}')"
                            class="group relative cursor-pointer"
                        >
                            <!-- Glassmorphism effect with glow -->
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-emerald-400 to-cyan-400 rounded-2xl blur opacity-0 group-hover:opacity-100 transition duration-500"></div>

                            <div class="relative backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl p-8 h-40 flex flex-col justify-center items-center shadow-2xl transform transition-all duration-300 group-hover:scale-105 group-hover:shadow-emerald-500/30 group-hover:bg-white/20">
                                <div class="text-center">
                                    <div class="text-7xl font-bold text-white drop-shadow-lg">
                                        {{ $order->number }}
                                    </div>
                                    <div class="text-sm font-semibold text-emerald-200 mt-2">
                                        READY FOR PICKUP
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
