<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserScope implements Scope
{

    protected $alias;

    public function __construct($alias = null)
    {
        $this->alias = $alias ? "$alias." : "";
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $userColumn = $this->alias . 'user_id'; // Dynamically apply alias to user_id

        if ($user->role === 'manager') {
            $user_ids = DB::table('users')->where('manager_id', $user->id)->pluck('id')->toArray();
            if (!empty($user_ids)) {
                $builder->whereIn($userColumn, $user_ids);
            }
        } elseif ($user->role === 'user') {
            $builder->where($userColumn, $user->id);
        }
    }
}
