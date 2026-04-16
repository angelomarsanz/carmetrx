<?php

namespace App\Models\User\Project;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    use HasFactory;
    public $table = "project_wishlists";
    protected $guarded = [];

    protected  $fillable = [
        'user_id',
        'customer_id',
        'project_id'
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function projectContent(): BelongsTo
    {
        return $this->belongsTo(ProjectContent::class, 'project_id', 'project_id');
    }



    /**
     * Create a new wishlist entry.
     *
     * @param int $customerId
     * @param int $userId
     * @param int $projectId
     * @return Wishlist
     */
    public  function createWishlist($customerId, $userId, $projectId)
    {
        return  self::create([
            'user_id' => $userId,
            'customer_id' => $customerId,
            'project_id' => $projectId
        ]);
    }
}
