<?php

namespace App\Enums;

enum UserRole: string {
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case AUTHOR = 'author';
    case CONTRIBUTOR = 'contributor';

    public function label(): string {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::ADMIN => 'Administrator',
            self::EDITOR => 'Executive Editor',
            self::AUTHOR => 'Author & Critic',
            self::CONTRIBUTOR => 'Artisan Contributor',
        };
    }
}
