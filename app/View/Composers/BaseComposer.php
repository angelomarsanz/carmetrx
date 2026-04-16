<?php

namespace App\View\Composers;

use App\Http\Helpers\UserPermissionHelper;
use App\Models\User\UserPermission;

abstract class BaseComposer
{
  protected function changePreferences($userId)
  {
    $currentPackage = UserPermissionHelper::currentPackage($userId);

    $preference = UserPermission::where('user_id', $userId)->first();

    if (!empty($currentPackage) && ($currentPackage->id != $preference->package_id)) {
      $preference->package_id = $currentPackage->id;

      $features = !empty($currentPackage->features) ? json_decode($currentPackage->features, true) : [];
      $features[] = "Contact";
      $preference->permissions = json_encode($features);
      $preference->save();
    }
  }
}
