<?php

namespace App\Models\User\Property;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Wishlist extends Model
{
    use HasFactory;

    protected  $table = 'property_wishlists';
    protected  $fillable = [
        'user_id',
        'customer_id',
        'property_id'
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }


    public function propertyContent(): BelongsTo
    {
        return $this->belongsTo(PropertyContent::class, 'property_id', 'property_id');
    }

    /**
     * Create a new wishlist entry.
     *
     * @param int $customerId
     * @param int $userId
     * @param int $propertyId
     * @return Wishlist
     */
    public  function createWishlist($customerId, $userId, $propetyId)
    {
        return  self::create([
            'user_id' => $userId,
            'customer_id' => $customerId,
            'property_id' => $propetyId
        ]);
    }
}
