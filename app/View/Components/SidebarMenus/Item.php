<?php

namespace App\View\Components\SidebarMenus;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Item extends Component
{
    public function __construct(
        public string $name,
        public string $route,
        public bool $active = false,
        public bool $danger = false,
        /** @var ?array{name: string, method: string, confirm?: bool} */
        public ?array $form = null,
    ) {
        if ($active && $danger) {
            throw new Exception('Sidebar item with "danger" attribute can not be active.');
        }

        if ($active && $form) {
            throw new Exception('Sidebar item with "form" attribute can not be active.');
        }
    }

    public function render(): View
    {
        return view('components.sidebar-menus.item');
    }
}
