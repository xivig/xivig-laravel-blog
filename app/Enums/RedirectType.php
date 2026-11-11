<?php

namespace App\Enums;

enum RedirectType: int {
    case PERMANENT = 301;
    case TEMPORARY = 302;
    case SEE_OTHER = 303;
    case TEMPORARY_REDIRECT = 307;
    case PERMANENT_REDIRECT = 308;

    public function label(): string {
        return match($this) {
            self::PERMANENT => '301 Moved Permanently (SEO Recommended)',
            self::TEMPORARY => '302 Found (Temporary)',
            self::SEE_OTHER => '303 See Other',
            self::TEMPORARY_REDIRECT => '307 Temporary Redirect',
            self::PERMANENT_REDIRECT => '308 Permanent Redirect',
        };
    }
}
