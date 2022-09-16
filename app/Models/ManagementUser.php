<?php

namespace App\Models;

use App\Models\Admin\Admin;
use App\Models\BigBrother\BigBrother;

class ManagementUser extends User
{
    protected $table = 'users';

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            $query->where('profile_type', BigBrother::$profilePath);
            $query->orWhere('profile_type', Admin::$profilePath);
        });
    }
}
