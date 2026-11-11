<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model {
    protected $fillable = ['query', 'results_count', 'ip_address'];

    public static function logQuery(string $query, int $resultsCount = 0): void {
        try {
            static::create([
                'query'         => mb_substr(trim($query), 0, 190),
                'results_count' => $resultsCount,
                'ip_address'    => request()->ip(),
            ]);
        } catch (\Throwable $e) {}
    }
}
