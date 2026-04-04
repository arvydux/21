<x-layouts.app :title="__('Nustatymai')">
    <div class="flex flex-col gap-6 max-w-lg">

        <div>
            <flux:heading size="xl" level="1">{{ __('Nustatymai') }}</flux:heading>
            <flux:subheading size="lg" class="mt-1 text-white/70">{{ __('Valdykite savo POS konfigūraciją') }}</flux:subheading>
            <flux:separator variant="subtle" class="mt-4" />
        </div>

        <div>
            <flux:heading class="mb-1">{{ __('Dizaino šablonas') }}</flux:heading>
            <flux:subheading class="mb-4 text-white/70">{{ __('Pasirinkite dizaino šabloną savo POS sąsajai') }}</flux:subheading>

            <livewire:template-picker />
        </div>

    </div>
</x-layouts.app>
