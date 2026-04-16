<?php

namespace App\Models\User\Agent;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Facades\Purifier;

class AgentInfo extends Model
{
    use HasFactory;
    public $table = "user_agent_infos";
    protected $guarded = [];

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }
    public function getFullNameAttribute()
    {
        return ucwords(trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')));
    }

    protected function details(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Purifier::clean($value),
        );
    }

    public static function sotreInfo($agentId, $langId, $requestData)
    {
        return self::create([
            'agent_id' => $agentId,
            'language_id' => $langId,
            'first_name' => $requestData['first_name'] ?? null,
            'last_name' => $requestData['last_name'] ?? null,
        ]);
    }
}
