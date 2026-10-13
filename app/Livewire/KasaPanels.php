<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class KasaPanels extends Component
{
    public const CACHE_KEY = 'pos_panel_widths';

    public const MENU_DEFAULT = 208;

    public const MENU_MIN = 160;

    public const MENU_MAX = 320;

    public const CART_DEFAULT = 384;

    public const CART_MIN = 300;

    public const CART_MAX = 560;

    public int $menuWidth = self::MENU_DEFAULT;

    public int $cartWidth = self::CART_DEFAULT;

    public function mount(): void
    {
        $widths = cache(self::CACHE_KEY, []);

        $this->menuWidth = $this->clampMenu((int) ($widths['menu'] ?? self::MENU_DEFAULT));
        $this->cartWidth = $this->clampCart((int) ($widths['cart'] ?? self::CART_DEFAULT));
    }

    /**
     * Stores the panel widths the cashier dragged to, kept within the allowed range.
     */
    #[Renderless]
    public function saveWidths(int $menuWidth, int $cartWidth): void
    {
        $this->menuWidth = $this->clampMenu($menuWidth);
        $this->cartWidth = $this->clampCart($cartWidth);

        cache()->forever(self::CACHE_KEY, ['menu' => $this->menuWidth, 'cart' => $this->cartWidth]);
    }

    private function clampMenu(int $width): int
    {
        return max(self::MENU_MIN, min(self::MENU_MAX, $width));
    }

    private function clampCart(int $width): int
    {
        return max(self::CART_MIN, min(self::CART_MAX, $width));
    }

    public function render(): View
    {
        return view('livewire.kasa-panels');
    }
}
