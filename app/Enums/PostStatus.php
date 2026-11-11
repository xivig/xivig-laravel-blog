<?php

namespace App\Enums;

enum PostStatus: string {
    case DRAFT = 'draft';
    case SCHEDULED = 'scheduled';
    case PUBLISHED = 'published';
    case PRIVATE = 'private';

    public function label(): string {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SCHEDULED => 'Scheduled',
            self::PUBLISHED => 'Published',
            self::PRIVATE => 'Private',
        };
    }

    public function badgeClass(): string {
        return match($this) {
            self::DRAFT => 'bg-secondary',
            self::SCHEDULED => 'bg-info',
            self::PUBLISHED => 'bg-success',
            self::PRIVATE => 'bg-warning text-dark',
        };
    }
}
