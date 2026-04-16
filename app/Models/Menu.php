<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }

    public function store($tenantId, $langId)
    {
        self::create([
            'language_id' => $langId,
            'user_id' => $tenantId,
            'menus' => json_encode(config('defaults.menus')),
        ]);
        return;
    }
}
