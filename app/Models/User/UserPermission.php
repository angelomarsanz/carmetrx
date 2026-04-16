<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    public $table = "user_permissions";
    protected $fillable = [
        'permissions',
        'package_id',
        'user_id'
    ];

    public function permissions(): Attribute
    {
        return Attribute::make(
            set: fn($value) => json_encode($value),
        );
    }

    public function store($tenantId, $packageId, $features)
    {
        return self::create([
            'user_id' => $tenantId,
            'package_id' => $packageId,
            'permissions' => $features,
        ]);
    }
}
