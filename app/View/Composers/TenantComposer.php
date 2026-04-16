<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\User\BasicSetting;
use Illuminate\Support\Facades\Auth;
use App\Traits\Tenant\TenantLanguage;
use App\Http\Helpers\UserPermissionHelper;
use App\Models\BasicSetting as AdminBasicSetting;
use App\Services\Tenant\PackageDowngradeService;

class TenantComposer extends BaseComposer
{
    use TenantLanguage;
    public function compose(View $view)
    {
        if (Auth::guard('web')->check()) {
            $tenant =  Auth::guard('web')->user();
            $userId = $tenant->id;
            // Change package_id in 'user_permissions'
            $this->changePreferences($userId);
            $currentLang = $this->currentLang();
            // Get user settings and package details
            $userBs = BasicSetting::where('user_id', $userId)->first();
            $userCurrentPackage = UserPermissionHelper::currentPackage($userId);
            $userFeaturesCount = UserPermissionHelper::userFeaturesCount($userId);
            $adminLangs = $this->allLangs();

            $adminBs = AdminBasicSetting::select('property_country_status','property_state_status')->first();


            // Prepare data for the view
            $view->with([

                'userBs' => $userBs,
                'userCurrentPackage' => $userCurrentPackage,
                'userFeaturesCount' => $userFeaturesCount,
                'currentLang' => $currentLang,
                'adminLangs' => $adminLangs,
                'tenant' => $tenant,
                'bs' => $currentLang->basic_setting,
                'be' => $currentLang->basic_extended,
                'adminBs' => $adminBs,
            ]);
        }
    }
}
