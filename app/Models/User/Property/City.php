<?php

namespace App\Models\User\Property;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class City extends Model
{
    use HasFactory;
    public $table = "user_cities";
    protected $guarded = [];

    public function contents()
    {
        return $this->hasMany(CityContent::class, 'city_id');
    }
    public function cityContent()
    {
        return $this->hasOne(CityContent::class, 'city_id', 'id');
    }

    public function getContent($langId)
    {
        return $this->contents()->where('language_id', $langId)->first();
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'city_id', 'id');
    }

    public function scopeGetCities($query, $tenantId, $languageId, $name = null)
    {
        return $query->where('user_id', $tenantId)
            ->when($name, function ($query, $name) use ($languageId) {
                $query->whereHas('contents', function ($q) use ($name, $languageId) {
                    $q->where('language_id', $languageId)
                        ->where('name', 'LIKE', "%{$name}%");
                });
            })
            ->orderBy('serial_number', 'asc')->get()->map(
                function ($item) use ($languageId) {
                    $content = $item->getContent($languageId);
                    $item->name = optional($content)->name;
                    $item->country_name = $item->country?->getContent($languageId)?->name;
                    $item->state_name = $item->state?->getContent($languageId)?->name;

                    return $item;
                }
            )->filter(function ($item) {
                return $item->name !== null;
            });
    }

    public function deleteCity()
    {
        $city = $this;
        $properties = $city->properties()->count();

        if ($properties >  0) {
            $delete = false;
        } else {
            $delete = true;
        }
        if ($delete) {
            DB::transaction(
                function () use ($city) {
                    $city->contents()->delete();
                    @unlink(public_path('assets/img/property-city/') . $city->image);
                    $city->delete();
                }
            );
        }
        return $delete;
    }
}
