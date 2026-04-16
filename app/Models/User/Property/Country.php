<?php

namespace App\Models\User\Property;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;
    public $table = "user_countries";
    protected $guarded = [];

    public function contents()
    {
        return $this->hasMany(CountryContent::class, 'country_id');
    }

    public function countryContent()
    {
        return $this->hasOne(CountryContent::class, 'country_id', 'id');
    }

    public function getContent($langId)
    {
        return $this->contents()->where('language_id', $langId)->first();
    }
    public function states()
    {
        return $this->hasMany(State::class, 'country_id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'country_id');
    }
    public function cities()
    {
        return $this->hasMany(City::class, 'country_id');
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
            ->orderBy('id', 'desc')->get()->map(
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
        $properties = $country->properties()->count();
        $cities = $country->cities()->count();
        $states = $country->states()->count();

        if ($properties >  0 || $cities >  0 || $states >  0) {
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
