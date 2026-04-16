<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Support\Facades\Auth;

class TenantFrontLangBlade
{
  use  TenantFrontendLanguage;
  public function compose(View $view)
  {
    if (Auth::guard('web')->check()) {
      $tenantId = Auth::guard('web')->user()->id;
    } elseif (Auth::guard('agent')->check()) {
      $tenantId = Auth::guard('agent')->user()->user_id;
    }
    $tenantLanguages = $this->allLangs($tenantId);
    $tenantDefaultLang = $this->defaultLang($tenantId);

    $view->with('tenantLanguages', $tenantLanguages);
    $view->with('tenantDefaultLang', $tenantDefaultLang);
  }
}
