<?php

namespace App\Enums;

enum MenuLocation: string {
    case HEADER = 'header';
    case FOOTER = 'footer';
    case SIDEBAR = 'sidebar';
    case MOBILE = 'mobile';

    public function label(): string {
        return match ($this) {
            self::HEADER => 'Primary Header Masthead',
            self::FOOTER => 'Footer Directory',
            self::SIDEBAR => 'Editorial Sidebar',
            self::MOBILE => 'Mobile Navigation Drawer',
        };
    }
}
