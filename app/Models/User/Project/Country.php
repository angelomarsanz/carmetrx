<?php

namespace App\Models\User\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Country extends Model
{
    use HasFactory;
    public $table = "user_project_countries";
    protected $guarded = [];

    public function contents()
    {
        return $this->hasMany(CountryContent::class, 'country_id', 'id');
    }
    public function countryContent()
    {
        return $this->hasOne(CountryContent::class, 'country_id', 'id');
    }

    public function getContent($langId)
    {
        return $this->contents()->where('language_id', $langId)->first();
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'country_id');
    }

    public function states()
    {
        return $this->hasMany(State::class, 'country_id');
    }
    public function projects()
    {
        return $this->hasMany(Project::class, 'country_id');
    }
    public function scopeGetCountries($query, $tenantId, $languageId, $name = null)
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
                    return $item;
                }
            )->filter(function ($item) {
                return $item->name !== null;
            });
    }

    public function deleteCountry()
    {
        $country = $this;
        $projects = $country->projects()->count();
        $cities = $country->cities()->count();
        $states = $country->states()->count();

        if ($projects >  0 || $cities >  0 || $states >  0) {
            $delete = false;
        } else {
            $delete = true;
        }
        if ($delete) {
            DB::transaction(
                function () use ($country) {
                    $country->contents()->delete();
                    $country->delete();
                }
            );
        }
        return $delete;
    }
}
