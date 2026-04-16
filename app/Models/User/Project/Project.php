<?php

namespace App\Models\User\Project;

use App\Models\User;
use App\Models\User\Agent\Agent;
use App\Models\User\BasicSetting;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory;
    public $table = "user_projects";
    protected $guarded = [];

    public  function basicInfo(): Attribute
    {
        return Attribute::make(
            get: function ($value) {

                return BasicSetting::where('user_id', $this->user_id)->first();
            },
        );
    }
    public function country()
    {
        return  $this->belongsTo(Country::class);
    }
    protected function isCountryActive(): Attribute
    {
        return Attribute::make(
            get: function ($value) {

                $county_status = $this->basicInfo->project_country_status; // Change this to match your attribute name

                $attributeName = 'country_id';
                // Check if the attribute exists and is not null
                if ($this->attributes[$attributeName] && $county_status == 1) {

                    return true;
                }

                return false; // Return false if the attribute is null or doesn't exist
            }
        );
    }

    public function state()
    {
        return  $this->belongsTo(State::class);
    }

    protected function isStateActive(): Attribute
    {
        return Attribute::make(
            get: function ($value) {

                $county_status = $this->basicInfo->project_state_status; // Change this to match your attribute name

                $attributeName = 'state_id';
                // Check if the attribute exists and is not null
                if ($this->attributes[$attributeName] && $county_status == 1) {

                    return true;
                }

                return false; // Return false if the attribute is null or doesn't exist
            },
        );
    }


    public static function storeProject($tenantId, $requestData)
    {

        return self::create([
            'user_id' => $tenantId,
            'agent_id' => $requestData['agent_id'] ?? null,
            'category_id' => $requestData['category_id'],
            'country_id' => $requestData['country_id'] ?? null,
            'state_id' => $requestData['state_id'] ?? null,
            'city_id' => $requestData['city_id'],
            'featured_image' => $requestData['featuredImgName'],
            'min_price' => $requestData['min_price'],
            'max_price' => $requestData['max_price'],
            'featured' => 0,
            'complete_status' => $requestData['status'],
            'latitude' => $requestData['latitude'],
            'longitude' => $requestData['longitude']
        ]);
    }

    public   function updateProject($requestData)
    {

        return $this->update([
            'category_id' => $requestData['category_id'],
            'country_id' => $requestData['country_id'] ?? null,
            'state_id' => $requestData['state_id'] ?? null,
            'city_id' => $requestData['city_id'],
            'agent_id' => $requestData['agent_id'] ?? null,
            'featured_image' => $requestData['featuredImgName'],
            'min_price' => $requestData['min_price'],
            'max_price' => $requestData['max_price'],
            'featured' => $requestData['featured'],
            'complete_status' => $requestData['status'],
            'latitude' => $requestData['latitude'],
            'longitude' => $requestData['longitude'],
        ]);
    }
    public  function  destroyProject()
    {
        DB::transaction(function () {
            $project = $this;

            // ==== delete project types ====
            $projectTypes =  $project->projectTypes()->get();
            foreach ($projectTypes as $type) {

                $type->deleteType();
            }

            // ==== delete project specification ====
            $specifications = $project->specifications()->get();
            foreach ($specifications as $specification) {
                $specification->deleteSpecification();
            }

            // ==== delete project contents ====
            $projectContents = $project->contents()->get();
            foreach ($projectContents as $content) {
                $content->delete();
            }
            // ===== delete wishlists under project =====
            $project->wishlists()->delete();

            // ==== delete project featured image ====
            if (!is_null($project->featured_image)) {
                @unlink(public_path('assets/img/project/featured/' . $project->featured_image));
            }

            // ==== delete project gallery images ====
            $projectGalleryImages  = $project->galleryImages()->get();
            foreach ($projectGalleryImages  as  $image) {
                @unlink(public_path('assets/img/project/gallery-images/' . $image->image));
                $image->delete();
            }

            // ==== delete project floorplan images ====
            $projectFloorplanImages  = $project->floorplanImages()->get();
            foreach ($projectFloorplanImages  as  $image) {
                @unlink(public_path('assets/img/project/floor-paln-images/' . $image->image));
                $image->delete();
            }

            $project->delete();
        });
        return;
    }

    public function galleryImages()
    {
        return $this->hasMany(ProjectGalleryImage::class, 'project_id', 'id');
    }

    public function floorplanImages()
    {
        return $this->hasMany(ProjectFloorplanImage::class, 'project_id', 'id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function projectTypes()
    {
        return $this->hasMany(ProjectType::class, 'project_id', 'id');
    }
    public function projectTypeContents()
    {
        return $this->hasManyThrough(ProjectTypeContent::class, ProjectType::class);
    }
    public function specifications()
    {
        return $this->hasMany(ProjectSpecification::class, 'project_id', 'id');
    }

    public function contents()
    {
        return $this->hasMany(ProjectContent::class);
    }


    public function getContent($langId)
    {
        return $this->contents()->where('language_id', $langId)->first();
    }
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'project_id', 'id');
    }
}
