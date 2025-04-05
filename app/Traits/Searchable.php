<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{

    /**
     * Apply search query on a model.
     *
     * @param  Builder  $query
     * @param  string|null  $searchTerm
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $searchTerm): Builder {
        if (!$searchTerm) {
            return $query;
        }
        return $query->where(function (Builder $query) use ($searchTerm) {
            foreach ($this->searchableFields as $field) {
                if (str_contains($field, '.')) {
                    // Handle relationship search
                    [$relation, $relationField] = explode('.', $field);
                    $query->orWhereHas($relation, function (Builder $query) use ($relationField, $searchTerm) {
                        $query->where($relationField, 'like', "%{$searchTerm}%");
                    });
                } else {
                    // Handle regular field search
                    $query->orWhere($field, 'like', "%{$searchTerm}%");
                }
            }
        });
    }

}
