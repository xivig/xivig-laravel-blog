<?php

namespace App\Helpers;

use App\Models\Setting;
use Carbon\Carbon;
use DateTimeZone;

class TimezoneHelper {
    /**
     * Get a comprehensive list of timezones grouped by region with GMT offset.
     */
    public static function list(): array {
        $timezones = [
            'UTC' => [
                'UTC' => '(UTC+00:00) UTC Standard Time',
            ],
            'Europe' => [
                'Europe/London'    => '(UTC+00:00 / +01:00) London, Dublin, Lisbon',
                'Europe/Paris'     => '(UTC+01:00 / +02:00) Paris, Brussels, Geneva, Madrid',
                'Europe/Berlin'    => '(UTC+01:00 / +02:00) Berlin, Frankfurt, Rome, Amsterdam',
                'Europe/Athens'    => '(UTC+02:00 / +03:00) Athens, Helsinki, Bucharest',
                'Europe/Moscow'    => '(UTC+03:00) Moscow, St. Petersburg',
            ],
            'Americas' => [
                'America/New_York'    => '(UTC-05:00 / -04:00) Eastern Time (New York, Miami, Toronto)',
                'America/Chicago'     => '(UTC-06:00 / -05:00) Central Time (Chicago, Dallas)',
                'America/Denver'      => '(UTC-07:00 / -06:00) Mountain Time (Denver, Phoenix)',
                'America/Los_Angeles' => '(UTC-08:00 / -07:00) Pacific Time (Los Angeles, San Francisco)',
                'America/Anchorage'   => '(UTC-09:00 / -08:00) Alaska',
                'America/Honolulu'    => '(UTC-10:00) Hawaii',
                'America/Sao_Paulo'   => '(UTC-03:00) São Paulo, Rio de Janeiro, Buenos Aires',
            ],
            'Asia & Middle East' => [
                'Asia/Dubai'       => '(UTC+04:00) Dubai, Abu Dhabi, Muscat',
                'Asia/Kolkata'     => '(UTC+05:30) Mumbai, New Delhi, Bengaluru, Colombo',
                'Asia/Bangkok'     => '(UTC+07:00) Bangkok, Jakarta, Hanoi',
                'Asia/Singapore'   => '(UTC+08:00) Singapore, Kuala Lumpur, Hong Kong, Beijing',
                'Asia/Tokyo'       => '(UTC+09:00) Tokyo, Seoul, Osaka',
                'Asia/Shanghai'    => '(UTC+08:00) Shanghai, Shenzhen, Taipei',
            ],
            'Australia & Pacific' => [
                'Australia/Sydney'   => '(UTC+10:00 / +11:00) Sydney, Melbourne, Brisbane',
                'Pacific/Auckland'   => '(UTC+12:00 / +13:00) Auckland, Wellington',
            ],
            'Africa' => [
                'Africa/Cairo'        => '(UTC+02:00 / +03:00) Cairo',
                'Africa/Johannesburg' => '(UTC+02:00) Johannesburg, Pretoria, Cape Town',
                'Africa/Lagos'        => '(UTC+01:00) Lagos, Abuja',
            ],
        ];

        return $timezones;
    }

    /**
     * Get active timezone string for the given user or system default.
     */
    public static function activeTimezone($user = null): string {
        if ($user && !empty($user->timezone)) {
            return $user->timezone;
        }

        if (auth()->check() && !empty(auth()->user()->timezone)) {
            return auth()->user()->timezone;
        }

        return Setting::get('timezone', config('app.timezone', 'UTC'));
    }

    /**
     * Format a given date/time into the active timezone.
     */
    public static function format($date, string $format = 'M d, Y h:i A', $user = null): string {
        if (!$date) {
            return '—';
        }

        $c = $date instanceof \Carbon\CarbonInterface ? $date->copy() : Carbon::parse($date);
        $tz = self::activeTimezone($user);

        try {
            return $c->setTimezone($tz)->format($format);
        } catch (\Throwable $e) {
            return $c->format($format);
        }
    }
}
