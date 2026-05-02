<?php

use Livewire\Volt\Component;

new class extends Component {
    public bool $onlyHotFood = false;

    public function mount(): void
    {
        $this->onlyHotFood = (bool) cache('kitchen_printer_only_hot_food', false);
    }

    public function updatedOnlyHotFood(): void
    {
        cache()->forever('kitchen_printer_only_hot_food', $this->onlyHotFood);
    }
}; ?>

<div class="flex items-center justify-between">
    <div class="flex flex-col gap-1">
        <flux:heading>{{ __('Spausdinti tik su karštu maistu') }}</flux:heading>
        <div class="flex items-center gap-2">
            @if($onlyHotFood)
                <flux:badge color="green" size="sm">Įjungta</flux:badge>
                <flux:subheading class="text-white/70">{{ __('Spausdina tik su čeburekais / kitais produktais') }}</flux:subheading>
            @else
                <flux:badge color="zinc" size="sm">Išjungta</flux:badge>
                <flux:subheading class="text-white/70">{{ __('Spausdina visada') }}</flux:subheading>
            @endif
        </div>
    </div>
    <flux:switch wire:model.live="onlyHotFood" />
</div>
