<?php

namespace App\Models\User;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubdomain extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function store($tenantId, $tenantUsername)
    {
        return self::create([
            'user_id' => $tenantId,
            'requested_subdomain' => $tenantUsername,
            'current_subdomain' => 0,
            'status' => 0,
        ]);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class, 'user_id', 'user_id');
    }
    public function website()
    {
        return $this->belongsTo(UserWebsite::class, 'website_id', 'id');
    }
}
