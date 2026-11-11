<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotFoundLog extends Model {
    protected $fillable = [
        'url',
        'referer',
        'ip_address',
        'user_agent',
        'hits',
        'last_seen_at',
    ];

    protected $casts = [
        'hits' => 'integer',
        'last_seen_at' => 'datetime',
    ];

    public static function logUrl(string $url, ?string $referer = null, ?string $ip = null, ?string $userAgent = null): void {
        $cleanUrl = '/' . ltrim(parse_url($url, PHP_URL_PATH) ?: '', '/');
        $record = self::firstOrNew(['url' => $cleanUrl]);
        $record->hits = $record->exists ? $record->hits + 1 : 1;
        $record->referer = $referer ?: $record->referer;
        $record->ip_address = $ip ?: $record->ip_address;
        $record->user_agent = $userAgent ? substr($userAgent, 0, 500) : $record->user_agent;
        $record->last_seen_at = now();
        $record->save();
    }
}
