<?php

namespace App\Traits\Tenant;

use App\Models\Language;

trait TenantLanguage
{
  protected $language;

  public function __construct()
  {
    $this->language = new Language();
  }

  public function currentLang()
  {
    $langCode = session('tenant_dashboard_lang');

    if ($langCode) {
      $language = $this->language->where('code', $langCode)->first();
    } else {
      $language = $this->language->where('is_default', 1)->first();
    }

    return $language;
  }

  public function defaultLang()
  {

    return  $this->language->where('is_default', 1)->first();
  }

  public function allLangs()
  {
    return  $this->language->get();
  }

  public function selectLang($langCode)
  {
    return  $this->language->where('code', $langCode)->firstOrFail();
  }


  public function agentCurrentLang()
  {
    $langCode = session('agent_dashboard_lang');

    if ($langCode) {
      $language = $this->language->where('code', $langCode)->first();
    } else {
      $language = $this->language->where('is_default', 1)->first();
    }

    return $language;
  }
}
