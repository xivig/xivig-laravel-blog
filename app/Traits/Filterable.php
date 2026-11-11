<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable {
    public function scopeFilter(Builder $query, array $filters): Builder {
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $term = '%' . $filters['search'] . '%';
                if (in_array('title', $this->fillable)) {
                    $q->orWhere('title', 'like', $term);
                }
                if (in_array('name', $this->fillable)) {
                    $q->orWhere('name', 'like', $term);
                }
                if (in_array('content', $this->fillable)) {
                    $q->orWhere('content', 'like', $term);
                }
            });
        }

        if (!empty($filters['status']) && in_array('status', $this->fillable)) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category_id']) && in_array('category_id', $this->fillable)) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query;
    }
}
