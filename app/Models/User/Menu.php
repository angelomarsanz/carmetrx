<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    use HasFactory;

    public $table = "user_menus";

    protected $fillable = [
        'language_id',
        'user_id',
        'menus'
    ];

   
    protected function menus(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value),
            set: fn($value) => json_encode($value),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function store($tenantId, $langId, $menus)
    {
        return self::create([
            'user_id' => $tenantId,
            'language_id' => $langId,
            'menus' => $menus,
        ]);
    }

    public static  function storeMenu($request, $tenantId)
    {
        return self::create([
            'user_id' => $tenantId,
            'language_id' =>  $request['language_id'],
            'menus' => $request['str'],
        ]);
    }

}
