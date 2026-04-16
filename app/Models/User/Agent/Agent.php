<?php

namespace App\Models\User\Agent;

use App\Constants\Constant;
use App\Models\User;
use App\Models\User\Project\Project;
use App\Models\User\Property\Property;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Agent extends Authenticatable
{
    use HasFactory;
    public $table = "user_agents";
    protected $guarded = [];

    public function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => !is_null($value) ? (Constant::AGENT_IMAGE . '/' . $value) : 'assets/img/blank-user.jpg'
        );
    }
    public function showPhoneNumber(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (bool) $value
        );
    }
    public function showEmailAddresss(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (bool) $value
        );
    }

    public function showContactForm(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (bool) $value
        );
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function agentInfo()
    {
        return $this->hasOne(AgentInfo::class, 'agent_id');
    }

    public function agentInfos()
    {
        return $this->hasMany(AgentInfo::class, 'agent_id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'agent_id');
    }
    public function propertyCount()
    {
       
        return $this->properties()->count();
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'agent_id');
    }

    public function projectCount()
    {
        return $this->projects()->count();
    }

    public function getMemberSinceAttribute()
    {

        return Carbon::parse($this->created_at)->format('F Y');
    }

    public function scopeSearch($query, $request, $userId, $languageId)
    {
        // Initialize an array to store matching agent IDs
        $agentIds = [];

        // Search by name in Agent and AgentInfo
        if ($request->filled('name')) {
            $name = $request->name;

            $usernae_infos = $this->where('username', 'like', '%' . $name . '%')->pluck('id');
            $name_infos =  AgentInfo::where('language_id', $languageId)
                ->where(function ($query) use ($name) {
                    $query->where('first_name', 'like', '%' . $name . '%')
                        ->orWhere('last_name', 'like', '%' . $name . '%');
                })
                ->pluck('agent_id');

            $agentIds = array_unique(array_merge($agentIds, $usernae_infos->toArray(), $name_infos->toArray()));
        }

        // Search by location in AgentInfo
        if ($request->filled('location')) {
            $location = $request->location;

            $agent_contents = AgentInfo::where(function ($q) use ($location) {
                $q->where('country', 'like', '%' . $location . '%')
                    ->orWhere('city', 'like', '%' . $location . '%')
                    ->orWhere('state', 'like', '%' . $location . '%')
                    ->orWhere('zip_code', 'like', '%' . $location . '%')
                    ->orWhere('address', 'like', '%' . $location . '%');
            })->pluck('agent_id');

            $agentIds = array_unique(array_merge($agentIds, $agent_contents->toArray()));

            // If no agents match the location, return an empty result
            if (empty($agentIds)) {
                return $query->whereRaw('1 = 0'); // returns no results
            }
        }

        // Filter by type if provided
        if ($request->filled('type')) {
            $type = $request->type;
            $agentTypeIds = DB::table('user_properties')->where('type', $type)->pluck('agent_id');
            $agentIds = array_unique(array_merge($agentIds, $agentTypeIds->toArray()));
        }

        // Final query with collected agent IDs and filters
        return $query->where('status', 1)
            ->where('user_id', $userId)
            ->with(['agentInfo' => function ($q) use ($languageId) {
                $q->where('language_id', $languageId);
            }])
            ->when(!empty($agentIds), function ($query) use ($agentIds) {
                return $query->whereIn('id', $agentIds);
            });
    }

    public function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Hash::make($value)
        );
    }
    public static function storeAgent($tenantId, $requestData)
    {
        return  self::create([
            'user_id' => $tenantId,
            'username' => $requestData['username'],
            'email' => $requestData['email'],
            'image' => $requestData['imageName'],
            'status' =>  $requestData['status'],
            'password' =>  $requestData['password']
        ]);
    }

    public function destroyAgent()
    {
        DB::transaction(function () {
            $agent = $this;
            // ====== delete all agent's projects ======
            $projects = Project::where('agent_id', $agent->id)->get();
            foreach ($projects as $project) {
                $project->destroyProject();
            }

            // ======= delete all agent's properties ====== 
            $properties = Property::where('agent_id', $agent->id)->get();
            foreach ($properties as $property) {
                $property->destroyPropertry();
            }

            // ===== delete agent infos =====
            $agent->agentInfos()->delete();

            // ===== delete agent image =====
            @unlink(public_path('/' . Constant::AGENT_IMAGE  . $agent->image));
            $agent->delete();
        });
        return;
    }
}
