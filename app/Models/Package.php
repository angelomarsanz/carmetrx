<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Package extends Model
{
    public $table = "packages";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function memberships()
    {
        return $this->hasMany('App\Models\Membership');
    }

    public function storePackage($request, $features)
    {
        return  self::create([
            'title' => $request['title'],
            'price' => $request['price'],
            'term' => $request['term'],
            'featured' => $request['featured'],
            'is_trial' => $request['is_trial'],
            'trial_days' => $request['trial_days'],
            'status' => $request['status'],
            'recommended' => $request['recommended'],
            'icon' => $request['icon'],
            'number_of_language' => $request['number_of_language'],
            'number_of_agent' => $request['number_of_agent'],
            'number_of_blog_post' => $request['number_of_blog_post'],
            'number_of_additional_page' => $request['number_of_additional_page'],
            'number_of_property' => $request['number_of_property'],
            'number_of_property_featured' => $request['number_of_property_featured'],
            'number_of_property_gallery_images' => $request['number_of_property_gallery_images'],
            'number_of_property_adittionl_specifications' => $request['number_of_property_additional_features'],
            'number_of_projects' => $request['number_of_projects'],
            'number_of_project_types' => $request['number_of_project_types'],
            'number_of_project_gallery_images' => $request['number_of_project_gallery_images'],
            'number_of_project_additionl_specifications' => $request['number_of_project_additional_features'],

            'features' => $features,
            'meta_keywords' => $request['meta_keywords'],
            'meta_description' => $request['meta_description'],
        ]);
    }

    public function updatePackage($request, $features)
    {
        return self::update([
            'title' => $request['title'],
            'price' => $request['price'],
            'term' => $request['term'],
            'featured' => $request['featured'],
            'is_trial' => $request['is_trial'],
            'trial_days' => $request['trial_days'],
            'status' => $request['status'],
            'recommended' => $request['recommended'],
            'icon' => $request['icon'],
            'number_of_language' => $request['number_of_language'],
            'number_of_agent' => $request['number_of_agent'],
            'number_of_blog_post' => $request['number_of_blog_post'],
            'number_of_additional_page' => $request['number_of_additional_page'],
            'number_of_property' => $request['number_of_property'],
            'number_of_property_featured' => $request['number_of_property_featured'],
            'number_of_property_gallery_images' => $request['number_of_property_gallery_images'],
            'number_of_property_adittionl_specifications' => $request['number_of_property_additional_features'],
            'number_of_projects' => $request['number_of_projects'],
            'number_of_project_types' => $request['number_of_project_types'],
            'number_of_project_gallery_images' => $request['number_of_project_gallery_images'],
            'number_of_project_additionl_specifications' => $request['number_of_project_additional_features'],

            'features' => $features,
            'meta_keywords' => $request['meta_keywords'],
            'meta_description' => $request['meta_description'],

        ]);
    }


    public function deletePacakage()
    {
        return DB::transaction(function () {
            $package = $this;
            if ($package->memberships()->count() > 0) {
                foreach ($package->memberships as $key => $membership) {
                    @unlink('assets/front/img/membership/receipt/' . $membership->receipt);
                    $membership->delete();
                }
            }
            $package->delete();
        });
    }
}
