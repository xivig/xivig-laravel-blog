<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\MenuItem;

class MenuService {
    public static function getMenu(string $location): ?Menu {
        return Menu::forLocation($location);
    }

    public static function addItem(Menu $menu, array $data): MenuItem {
        $order = $menu->items()->max('order') + 1;
        return $menu->items()->create(array_merge($data, ['order' => $order]));
    }
}
