<?php

namespace App\Enums;

enum CommentStatus: string {
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case SPAM = 'spam';
    case TRASH = 'trash';

    public function label(): string {
        return match($this) {
            self::PENDING => 'Pending Review',
            self::APPROVED => 'Approved',
            self::SPAM => 'Spam',
            self::TRASH => 'Trash',
        };
    }
}
