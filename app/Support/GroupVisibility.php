<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * The simple half of this app's visibility rule - global for Admin/Ground
 * Surveyor, else scoped to the user's own group_id - shared by resources
 * that don't need CargoDetail's fuller branching (channel-partner/consignee
 * columns don't exist on every resource). See CargoDetail::scopeVisibleTo()
 * for the fuller rule dispatches themselves still use.
 */
class GroupVisibility
{
    public static function apply(Builder $query, User $user, string $groupColumn = 'group_id'): Builder
    {
        if ($user->is_admin == 1 || $user->role === 'Ground Surveyor') {
            return $query;
        }

        return $query->where($groupColumn, $user->group_id);
    }
}
