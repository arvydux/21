<x-layouts.app :title="__('Nustatymai')">
    <div class="flex flex-col gap-6 max-w-lg">

        <div>
            <flux:heading size="xl" level="1">{{ __('Nustatymai') }}</flux:heading>
            <flux:subheading size="lg" class="mt-1 text-white/70">{{ __('Valdykite savo POS konfigūraciją') }}</flux:subheading>
            <flux:separator variant="subtle" class="mt-4" />
        </div>

        <div x-data="{ tab: 'dizainas' }">

            {{-- Tab buttons --}}
            <div class="flex gap-1 border-b border-white/15 mb-6">
                <button
                    @click="tab = 'dizainas'"
                    :class="tab === 'dizainas' ? 'border-b-2 border-white text-white' : 'text-white/50 hover:text-white/80'"
                    class="px-4 py-2 text-sm font-medium transition-colors -mb-px">
                    {{ __('Dizainas') }}
                </button>
                <button
                    @click="tab = 'mygtukai'"
                    :class="tab === 'mygtukai' ? 'border-b-2 border-white text-white' : 'text-white/50 hover:text-white/80'"
                    class="px-4 py-2 text-sm font-medium transition-colors -mb-px">
                    {{ __('Mygtukai') }}
                </button>
                <button
                    @click="tab = 'spausdintuvai'"
                    :class="tab === 'spausdintuvai' ? 'border-b-2 border-white text-white' : 'text-white/50 hover:text-white/80'"
                    class="px-4 py-2 text-sm font-medium transition-colors -mb-px">
                    {{ __('Spausdintuvai') }}
                </button>
            </div>

            {{-- Dizainas tab --}}
            <div x-show="tab === 'dizainas'" x-transition>
                <flux:heading class="mb-1">{{ __('Dizaino šablonas') }}</flux:heading>
                <flux:subheading class="mb-4 text-white/70">{{ __('Pasirinkite dizaino šabloną savo POS sąsajai') }}</flux:subheading>

                <livewire:template-picker />
            </div>

            {{-- Mygtukai tab --}}
            <div x-show="tab === 'mygtukai'" x-transition class="flex flex-col gap-6">
                <div>
                    <flux:heading class="mb-1">{{ __('Mygtukų dydis') }}</flux:heading>
                    <flux:subheading class="mb-4 text-white/70">{{ __('Pasirinkite produktų mygtukų aukštį kasos sąsajoje') }}</flux:subheading>

                    <livewire:button-style-picker />
                </div>

                <flux:separator variant="subtle" />

                <div>
                    <flux:heading class="mb-1">{{ __('Stulpelių skaičius') }}</flux:heading>
                    <flux:subheading class="mb-4 text-white/70">{{ __('Pasirinkite, kiek mygtukų rodoma vienoje eilutėje') }}</flux:subheading>

                    <livewire:button-columns-picker />
                </div>
            </div>

            {{-- Spausdintuvai tab --}}
            <div x-show="tab === 'spausdintuvai'" x-transition class="flex flex-col gap-6">
                <div>
                    <flux:heading class="mb-1">{{ __('Virtuvės spausdintuvas') }}</flux:heading>
                    <flux:subheading class="mb-4 text-white/70">{{ __('Konfigūruokite virtuvės spausdintuvo elgesį') }}</flux:subheading>

                    <livewire:kitchen-printer-settings />
                </div>

                <flux:separator variant="subtle" />

                <div>
                    <flux:heading class="mb-1">{{ __('Kliento spausdintuvas') }}</flux:heading>
                    <flux:subheading class="mb-4 text-white/70">{{ __('Konfigūruokite kliento spausdintuvo elgesį') }}</flux:subheading>

                    <livewire:client-printer-settings />
                </div>
            </div>

        </div>

    </div>
</x-layouts.app>
