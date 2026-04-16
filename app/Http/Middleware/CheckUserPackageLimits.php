<?php

namespace App\Http\Middleware;

use App\Http\Helpers\UserPermissionHelper;
use App\Models\User;
use App\Models\User\Agent\Agent;
use App\Models\User\Project\Project;
use App\Models\User\Property\Property;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPackageLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $feature, $method)
    {
        
        if (Auth::check()) {
            if (Auth::guard('web')->user()) {

                $user = User::find(Auth::guard('web')->user()->id);
            } elseif (Auth::guard('agent')->user()) {
                $user = User::find(Auth::guard('agent')->user()->user_id);
            }

            $package = UserPermissionHelper::currentPackage($user->id);

            if (empty($package)) {
                return redirect()->route('user-dashboard');
            }
            $userFeaturesCount = UserPermissionHelper::userFeaturesCount($user->id);

            if ($method == 'store') {

                if ($feature == 'language') {
                    if (($package->number_of_language > $userFeaturesCount['languages'] || $package->number_of_language == 999999) && $this->checkFeaturesNotDowngraded($user->id, $feature, $package, $userFeaturesCount, $method)) {
                        return $next($request);
                    } else {
                        return $this->handleDowngradeResponse($request);
                    }
                }

                if ($feature == 'blog') {
                    if (($package->number_of_blog_post > $userFeaturesCount['blogs'] || $package->number_of_blog_post == 999999) && $this->checkFeaturesNotDowngraded($user->id, $feature, $package, $userFeaturesCount, $method)) {
                        return $next($request);
                    } else {
                        return $this->handleDowngradeResponse($request);
                    }
                }

                if ($feature == 'customPage') {
                    if (($package->number_of_additional_page > $userFeaturesCount['customPages'] || $package->number_of_additional_page == 999999) && $this->checkFeaturesNotDowngraded($user->id, $feature, $package, $userFeaturesCount, $method)) {
                        return $next($request);
                    } else {
                        return $this->handleDowngradeResponse($request);
                    }
                }

                if ($feature == 'agent') {

                    if (($package->number_of_agent > $userFeaturesCount['agents'] || $package->number_of_agent == 999999) && $this->checkFeaturesNotDowngraded($user->id, $feature, $package, $userFeaturesCount, $method)) {
                        return $next($request);
                    } else {
                        return $this->handleDowngradeResponse($request);
                    }
                }

                if ($feature == 'property') {

                    if (($package->number_of_property > $userFeaturesCount['properties'] || $package->number_of_property == 999999) && $this->checkFeaturesNotDowngraded($user->id, $feature, $package, $userFeaturesCount, $method)) {
                        return $next($request);
                    } else {
                        return $this->handleDowngradeResponse($request);
                    }
                }

                if ($feature == 'project') {
                    if (($package->number_of_projects > $userFeaturesCount['projects'] || $package->number_of_projects == 999999) && $this->checkFeaturesNotDowngraded($user->id, $feature, $package, $userFeaturesCount, $method)) {
                        return $next($request);
                    } else {
                        return $this->handleDowngradeResponse($request);
                    }
                }
                if ($feature == 'projectType') {
                    $project = Project::with(['projectTypes'])->where('user_id', $user->id)->find($request->project_id);
                    $projectCount = $project->projectTypes()->count();
                    if (($package->number_of_project_types > $projectCount || $package->number_of_project_types == 999999) &&  $this->checkFeaturesNotDowngraded($user->id, $feature, $package, $userFeaturesCount, $method)) {
                        return $next($request);
                    } else {
                        return $this->handleDowngradeResponse($request);
                    }
                }
            }

            if ($method == 'update') {

                if ($feature == 'language') {

                    if (($package->number_of_language >= $userFeaturesCount['languages'] || $package->number_of_language == 999999) && $this->checkFeaturesNotDowngraded($user->id, $feature, $package, $userFeaturesCount, $method)) {
                        return $next($request);
                    } else {
                        return $this->handleDowngradeResponse($request);
                    }
                }

                if ($feature == 'blog') {

                    if (($package->number_of_blog_post >= $userFeaturesCount['blogs'] || $package->number_of_blog_post == 999999) && $this->checkFeaturesNotDowngraded($user->id, $feature, $package, $userFeaturesCount, $method)) {
                        return $next($request);
                    } else {
                        return $this->handleDowngradeResponse($request);
                    }
                }

                if ($feature == 'customPage') {

                    if (($package->number_of_additional_page >= $userFeaturesCount['customPages'] || $package->number_of_additional_page == 999999) && $this->checkFeaturesNotDowngraded($user->id, $feature, $package, $userFeaturesCount, $method)) {
                        return $next($request);
                    } else {
                        return $this->handleDowngradeResponse($request);
                    }
                }

                if ($feature == 'agent') {

                    if (($package->number_of_agent >= $userFeaturesCount['agents'] || $package->number_of_agent == 999999) && $this->checkFeaturesNotDowngraded($user->id, $feature, $package, $userFeaturesCount, $method)) {
                        return $next($request);
                    } else {
                        return $this->handleDowngradeResponse($request);
                    }
                }

                if ($feature == 'property') {
                    // dd('wait');
                    if (($package->number_of_property >= $userFeaturesCount['properties'] || $package->number_of_property == 999999) && $this->checkFeaturesNotDowngraded($user->id, $feature, $package, $userFeaturesCount, $method)) {
                        return $next($request);
                    } else {
                        return $this->handleDowngradeResponse($request);
                    }
                }

                if ($feature == 'project') {
                    if (($package->number_of_projects >= $userFeaturesCount['projects'] || $package->number_of_projects == 999999) && $this->checkFeaturesNotDowngraded($user->id, $feature, $package, $userFeaturesCount, $method)) {
                        return $next($request);
                    } else {
                        return $this->handleDowngradeResponse($request);
                    }
                }
                if ($feature == 'projectType') {
                    $project = Project::with(['projectTypes'])->where('user_id', $user->id)->find($request->project_id);
                    $projectCount = $project->projectTypes()->count();
                    if (($package->number_of_project_types >= $projectCount || $package->number_of_project_types == 999999) &&  $this->checkFeaturesNotDowngraded($user->id, $feature, $package, $userFeaturesCount, $method)) {
                        return $next($request);
                    } else {
                        return $this->handleDowngradeResponse($request);
                    }
                }
            }
        }
    }
    private function checkFeaturesNotDowngraded($userId, $feature, $package, $userFeaturesCount, $method)
    {
        $return = true;
        if ($feature != 'language') {
            if ($package->number_of_language != 999999 && $package->number_of_language < $userFeaturesCount['languages']) {

                return  $return = false;
            }
        }

        if ($feature != 'blog') {
            if ($package->number_of_blog_post != 999999 && $package->number_of_blog_post < $userFeaturesCount['blogs']) {
                return  $return = false;
            }
        }

        if ($feature != 'customPage') {
            if ($package->number_of_additional_page != 999999 && $package->number_of_additional_page < $userFeaturesCount['customPages']) {
                return  $return = false;
            }
        }


        if ($feature != 'agent') {
            if ($package->number_of_agent != 999999 && $package->number_of_agent < $userFeaturesCount['agents']) {
                return  $return = false;
            }
        }

        if ($feature != 'property') {
            if ($package->number_of_property != 999999 && $package->number_of_property < $userFeaturesCount['properties']) {
                return  $return = false;
            }
        }
        if ($feature != 'project') {
            if ($package->number_of_projects != 999999 && $package->number_of_projects < $userFeaturesCount['projects']) {
                return  $return = false;
            }
        }


        // images and additional specofication check 
        $projects = Project::with(['galleryImages', 'floorplanImages', 'specifications', 'projectTypes'])->where('user_id', $userId)->get();
        $properties = Property::with(['galleryImages', 'specifications'])->where('user_id', $userId)->get();


        if ($properties) {

            foreach ($properties as $key => $property) {
                $propertytGalleryImages = $property->galleryImages()->count();
                $propertytSpecifications = $property->specifications()->count();

                if ($package->number_of_property_gallery_images != 999999 && ($property->galleryImages->isNotEmpty() && $package->number_of_property_gallery_images < $propertytGalleryImages)) {
                    return  $return = false;
                }
                if ($package->number_of_property_adittionl_specifications != 999999 && ($property->specifications->isNotEmpty() && $package->number_of_property_adittionl_specifications < $propertytSpecifications)) {
                    return  $return = false;
                }
            }
        }


        if ($projects) {
            foreach ($projects as $project) {
                $projectGalleryImages = $project->galleryImages()->count();
                $projectSpecifications = $project->specifications()->count();
                $projectTypes = $project->projectTypes()->get();
                $projectTypeCount = count($projectTypes);

                if ($package->number_of_project_gallery_images != 999999 && ($project->galleryImages->isNotEmpty() && $package->number_of_project_gallery_images < $projectGalleryImages)) {
                    return  $return = false;
                }
                if ($package->number_of_project_additionl_specifications != 999999 && ($project->specifications->isNotEmpty() && $package->number_of_project_additionl_specifications < $projectSpecifications)) {
                    return  $return = false;
                }
                if ($package->number_of_project_types != 999999 && ($project->projectTypes->isNotEmpty() && $package->number_of_project_types < $projectTypeCount)) {
                    return  $return = false;
                }
            }
        }

        return $return;
    }

    private function handleDowngradeResponse(Request $request)
    {
        if ($request->ajax()) {
            return response()->json('downgrade');
        } else {
            session()->flash('downgrade', __('Listing limit reached or exceeded!'));
            return redirect()->back();
        }
    }
}
