<?php

use Livewire\Volt\Component;

new class extends Component {
    public bool $enabled = true;

    public function mount(): void
    {
        $this->enabled = (bool) cache('client_printer_enabled', true);
    }

    public function updatedEnabled(): void
    {
        cache()->forever('client_printer_enabled', $this->enabled);
    }
}; ?>

<div class="flex items-center justify-between">
    <div class="flex flex-col gap-1">
        <flux:heading>{{ __('Kliento spausdintuvas') }}</flux:heading>
        <div class="flex items-center gap-2">
            @if($enabled)
                <flux:badge color="green" size="sm">Įjungta</flux:badge>
                <flux:subheading class="text-white/70">{{ __('Spausdina kvitą klientui') }}</flux:subheading>
            @else
                <flux:badge color="zinc" size="sm">Išjungta</flux:badge>
                <flux:subheading class="text-white/70">{{ __('Kvitas nespausdinamas') }}</flux:subheading>
            @endif
        </div>
    </div>
    <flux:switch wire:model.live="enabled" />
</div>
