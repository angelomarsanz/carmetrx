<?php

namespace App\Models\User\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyContact extends Model
{
    use HasFactory;
    public $table = 'user_property_contacts';

    public $fillable = ['user_id', 'agent_id', 'property_id', 'name', 'phone', 'email', 'message'];

    public function createContact($userId, $agentId, $request)
    {
        self::create([
            'user_id' => $userId,
            'agent_id' => $agentId,
            'property_id' => $request['property_id'],
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'message' => $request['message'],
        ]);
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'id');
    }
    public function propertyContent($langId = null)
    {
        return $this->belongsTo(PropertyContent::class, 'property_id', 'property_id')
            ->when($langId, function ($query) use ($langId) {
                $query->where('language_id', $langId);
            });
    }
}
