<?php

namespace App\View\Composers;

use App\Http\Helpers\UserPermissionHelper;
use App\Models\Language;
use App\Models\User\BasicSetting;
// use App\Models\User\Language as UserLanguage;
use App\Traits\Tenant\TenantLanguage;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AgentComposer extends BaseComposer
{
  use TenantLanguage;
  public function compose(View $view)
  {
    // Set theme version in session if not already set
    if (is_null(Session::get('agent_theme_version'))) {
      Session::put('agent_theme_version', 'light');
    }

    // Get languages
    $adminLangs = $this->allLangs();
    $adminCurrentLang = $this->agentCurrentLang();
    // dd(app()->getLocale());
    // $userDefaultLang = UserLanguage::where('is_default', 1)->first();

    // Determine agent dashboard language
    // if (session()->has('user_agent_lang')) {
    //   $agentDashboardLang = Language::where('code', session()->get('user_agent_lang'))->first();
    // } else {
    //   $agentDashboardLang = Language::where('is_default', 1)->first();
    //   session()->put('user_agent_lang', $agentDashboardLang->code);
    // }

    // Decode keywords
    // $keywords = json_decode($agentDashboardLang->user_keywords, true);

    // Get authenticated agent
    $agent = Auth::guard('agent')->user();
    $websiteSettings = BasicSetting::where('user_id', $agent->user_id ?? null)->first();

    $userCurrentPackage = UserPermissionHelper::currentPackage($agent->user_id);
    $userFeaturesCount = UserPermissionHelper::userFeaturesCount($agent->user_id);
    $view->with('currentPackage', $userCurrentPackage);
    $view->with('featuresCount', $userFeaturesCount);
    $view->with('adminLangs', $adminLangs);
    $view->with('adminCurrentLang', $adminCurrentLang);
    $view->with('adminBs', $adminCurrentLang->basic_setting); 
    // Prepare data for the view
    if ($agent) {
      // $dowgraded = UserPermissionHelper::packagesDowngraded($agent->user_id);
      $view->with([
        // 'currentPackage' => $dowgraded['userCurrentPackage'],
        // 'proGalImgDown' => $dowgraded['proGalImgDown'],
        // 'proSpeciDown' => $dowgraded['proSpeciDown'],
        // 'proImgCount' => $dowgraded['proImgCount'],
        // 'proSpeciCount' => $dowgraded['proSpeciCount'],
        // 'projectImgCount' => $dowgraded['projectImgCount'],
        // 'projectSpeciCount' => $dowgraded['projectSpeciCount'],
        // 'projectGalImgDown' => $dowgraded['projectGalImgDown'],
        // 'projectSpeciDown' => $dowgraded['projectSpeciDown'],
        // 'projectTypeDown' => $dowgraded['projectTypeDown'],
        // 'projectTypeCount' => $dowgraded['projectTypeCount'],
        // 'userCurrentPackage' => $dowgraded['userCurrentPackage'],
        // 'featuresCount' => $dowgraded['userFeaturesCount'], 
        // 'keywords' => $keywords,
        // 'language' => $userDefaultLang,
        'settings' => $websiteSettings,
        'agent' => $agent,
      ]);
    } else {
      $view->with('userCurrentPackage', true);


      // $view->with('keywords', $keywords);
      // $view->with('language', $userDefaultLang);
      $view->with('settings', $websiteSettings);
      $view->with('agent', $agent);
    }
  }
}
