<?php

use App\Http\Middleware\EnsureOnlineOrdersEnabled;
use Livewire\Volt\Component;

new class extends Component {
    public bool $enabled = true;

    public function mount(): void
    {
        $this->enabled = EnsureOnlineOrdersEnabled::isEnabled();
    }

    public function updatedEnabled(): void
    {
        cache()->forever(EnsureOnlineOrdersEnabled::CACHE_KEY, $this->enabled);
    }
}; ?>

<div class="flex items-center justify-between">
    <div class="flex flex-col gap-1">
        <flux:heading>{{ __('Užsakymo sekimas telefonu') }}</flux:heading>
        <div class="flex items-center gap-2">
            @if($enabled)
                <flux:badge color="green" size="sm">Įjungta</flux:badge>
                <flux:subheading class="text-white/70">{{ __('Kvite yra QR kodas, klientai seka užsakymą telefonu') }}</flux:subheading>
            @else
                <flux:badge color="zinc" size="sm">Išjungta</flux:badge>
                <flux:subheading class="text-white/70">{{ __('Kvite nėra QR kodo, užsakymų puslapiai uždaryti') }}</flux:subheading>
            @endif
        </div>
    </div>
    <flux:switch wire:model.live="enabled" />
</div>
