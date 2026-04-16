<?php

namespace App\Services\Tenant;


use App\Http\Helpers\UserPermissionHelper;
use App\Models\User\Agent\Agent;
use App\Models\User\CustomPage\Page;
use App\Models\User\Journal\Blog;
use App\Models\User\Language;
use App\Models\User\Project\Project;
use App\Models\User\Property\Property;

class PackageDowngradeService
{
  /**
   * Check for package downgrades.
   *
   * @param int $tenantId
   * @return array
   */
  public function checkDowngradeStatus(int $tenantId): array
  {
    $userCurrentPackage = UserPermissionHelper::currentPackage($tenantId);
    if (!$userCurrentPackage) {
      return $this->getDefaultDowngradedStatus();
    }

    $propertyData = Property::where('user_id', $tenantId)
      ->withCount(['galleryImages', 'specifications', 'featureds'])
      ->get(['id', 'gallery_images_count', 'specifications_count', 'featureds_count']);
    $proFeatureCount = Property::where([['user_id', $tenantId], ['featured', 1]])->count();

    $projectData = Project::where('user_id', $tenantId)
      ->withCount(['galleryImages', 'specifications', 'projectTypes'])
      ->get(['id', 'gallery_images_count', 'specifications_count', 'project_types_count']);
    $agentData = Agent::where('user_id', $tenantId)->get();
    $langData = Language::where('user_id', $tenantId)->where('is_admin', 0)->get();
    $blogData = Blog::where('user_id', $tenantId)->get();
    $customPageData = Page::where('user_id', $tenantId)->get();
    // Check property and project statuses
    $propertyStatus = $this->checkModelRelationAgainstPackage(Property::class, $propertyData, $userCurrentPackage, [
      'gallery_images' => 'number_of_property_gallery_images',
      'specifications' => 'number_of_property_adittionl_specifications',
      'featureds' => 'number_of_property_featured'
    ]);


    $projectStatus = $this->checkModelRelationAgainstPackage(Project::class, $projectData, $userCurrentPackage, [
      'gallery_images' => 'number_of_project_gallery_images',
      'specifications' => 'number_of_project_additionl_specifications',
      'project_types' => 'number_of_project_types',
    ]);

    $property = $this->checkModelCountAgainstLimit(Property::class, $propertyData, $userCurrentPackage->number_of_property);
    $project = $this->checkModelCountAgainstLimit(Project::class, $projectData, $userCurrentPackage->number_of_projects);
    $agent = $this->checkModelCountAgainstLimit(Agent::class, $agentData, $userCurrentPackage->number_of_agent);
    $language = $this->checkModelCountAgainstLimit(Language::class, $langData, $userCurrentPackage->number_of_language);
    $blog = $this->checkModelCountAgainstLimit(Blog::class, $blogData, $userCurrentPackage->number_of_blog_post);
    $customPage = $this->checkModelCountAgainstLimit(Page::class, $customPageData, $userCurrentPackage->number_of_additional_page);


    $dowgradedService = array_merge($propertyStatus, $projectStatus, $property, $agent, $project, $language, $blog, $customPage);

    // this is for agent model count $data['agentCount'] = $dowgradedService['Agent_count'];
    $data = [
      'customPageDown' => $dowgradedService['Page_down'],
      'languageDown' => $dowgradedService['Language_down'],
      'blogDown' => $dowgradedService['Blog_down'],
      'agentDown' => $dowgradedService['Agent_down'],
      'propertyDown' => $dowgradedService['Property_down'],
      'projectDown' => $dowgradedService['Project_down'],
      'profeaturedDown' => $proFeatureCount > $userCurrentPackage->number_of_property_featured ? true : false,
      'proGalImgDown' => $dowgradedService['Property_gallery_images_down'],
      'proSpeciDown' => $dowgradedService['Property_specifications_down'],
      'proImgCount' => $dowgradedService['Property_gallery_images_count'],
      'proSpeciCount' => $dowgradedService['Property_specifications_count'],

      'projectImgCount' => $dowgradedService['Project_gallery_images_count'],
      'projectSpeciCount' => $dowgradedService['Project_specifications_count'],
      'projectTypeCount' => $dowgradedService['Project_project_types_count'],
      'projectGalImgDown' => $dowgradedService['Project_gallery_images_down'],
      'projectSpeciDown' => $dowgradedService['Project_specifications_down'],
      'projectTypeDown' => $dowgradedService['Project_project_types_down'],

      'customPageLeft' => $userCurrentPackage->number_of_additional_page - $dowgradedService['Page_count'],
      'languageLeft' => $userCurrentPackage->number_of_language - $dowgradedService['Language_count'],
      'blogLeft' => $userCurrentPackage->number_of_blog_post - $dowgradedService['Blog_count'],
      'agentLeft' => $userCurrentPackage->number_of_agent - $dowgradedService['Agent_count'],
      'propertiesLeft' => $userCurrentPackage->number_of_property - $dowgradedService['Property_count'],
      'featuredPropertiesLeft' => $userCurrentPackage->number_of_property_featured - $dowgradedService['Property_featureds_count'],
      'projectLeft' => $userCurrentPackage->number_of_projects - $dowgradedService['Project_count'],
    ];
    return  $data;
  }

  /**
   * Get default downgraded status when no package exists.
   */
  private function getDefaultDowngradedStatus(): array
  {
    return [
      'projectGalImgDown' => true,
      'projectTypeDown' => true,
      'projectSpeciDown' => true,
      'projectImgCount' => 0,
      'projectTypeCount' => 0,
      'projectSpeciCount' => 0,
      'proGalImgDown' => true,
      'proSpeciDown' => true,
      'proImgCount' => 0,
      'proSpeciCount' => 0,
      'agentCount' => 0,
      'languageCount' => 0,
      'blogCount' => 0,
      'PageCount' => 0
    ];
  }

  /**
   * Check items against package limits.
   */
  private function checkModelRelationAgainstPackage($modelClass, $items, $package, $limits): array
  {
    $status = [];
    // $modelName = $items->isNotEmpty() ? class_basename($items->first()) : 'UnknownModel'; // Get model name
    $modelName =  class_basename($modelClass); // Get model name

    foreach ($limits as $relation => $packageLimitField) {
      $totalCount = 0;
      $isDowngraded = false;

      foreach ($items as $item) {
        $relationCount = $item->{$relation . '_count'};
        $totalCount += $relationCount;

        if ($relationCount > $package->$packageLimitField) {
          $isDowngraded = true;
          break;
        }
      }

      $status[$modelName . '_' . $relation . '_down'] = $isDowngraded; // Include model name in key
      $status[$modelName . '_' . $relation . '_count'] = $totalCount; // Include model name in key
    }

    return $status;
  }

  private  function checkModelCountAgainstLimit($modelClass, $items, $packageLimit)
  {
    // Count the number of model
    $modelCount = $items->count();
    // $modelName = $model->isNotEmpty() ? class_basename($model->first()) : 'UnknownModel';
    $modelName =  class_basename($modelClass);
    // Check if the count exceeds the limit
    $isDowngraded = $modelCount > $packageLimit;
    return [
      $modelName . '_count' => $modelCount,
      $modelName . '_down' => $isDowngraded,
    ];
  }
}
