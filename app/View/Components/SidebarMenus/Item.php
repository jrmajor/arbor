<?php

namespace App\View\Components\SidebarMenus;

use Exception;
use Illuminate\View\Component;

class Item extends Component
{
    public function __construct(
        public string $name,
        public string $route,
        public bool $active = false,
        public bool $danger = false,
        public ?array $form = null,
    ) {
        if ($active && $danger) {
            throw new Exception();
        }

        if ($active && $form) {
            throw new Exception();
        }
    }

    public function render()
    {
        return view('components.sidebar-menus.item');
    }
}
