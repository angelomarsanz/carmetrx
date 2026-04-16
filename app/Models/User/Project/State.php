<?php

namespace App\Models\User\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class State extends Model
{
    use HasFactory;
    public $table = "user_project_states";
    protected $guarded = [];

    public function contents()
    {
        return $this->hasMany(StateContent::class, 'state_id', 'id');
    }
    public function stateContent()
    {
        return $this->hasOne(StateContent::class, 'state_id', 'id');
    }
    public function getContent($langId)
    {
        return $this->contents()->where('language_id', $langId)->first();
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
    public function cities()
    {
        return $this->hasMany(City::class, 'state_id', 'id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'state_id', 'id');
    }

    public function scopeGetStates($query, $tenantId, $languageId, $name = null)
    {
        return $query->where('user_id', $tenantId)
            ->when($name, function ($query, $name) use ($languageId) {
                $query->whereHas('contents', function ($q) use ($name, $languageId) {
                    $q->where('language_id', $languageId)
                        ->where('name', 'LIKE', "%{$name}%");
                });
            })
            ->orderBy('id', 'asc')->get()->map(
                function ($item) use ($languageId) {
                    $content = $item->getContent($languageId);
                    $item->name = optional($content)->name;
                    $item->country_name = $item->country?->getContent($languageId)?->name;

                    return $item;
                }
            )->filter(function ($item) {
                return $item->name !== null;
            });
    }

    public function deleteState()
    {
        $state = $this;
        $projects = $state->projects()->count();
        $cities = $state->cities()->count();

        if ($projects >  0 || $cities >  0) {
            $delete = false;
        } else {
            $delete = true;
        }
        if ($delete) {
            DB::transaction(
                function () use ($state) {
                    $state->contents()->delete();
                    $state->delete();
                }
            );
        }
        return $delete;
    }
}
