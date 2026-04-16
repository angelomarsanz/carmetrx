<?php

namespace App\Traits\Tenant\Frontend;

use App\Models\User\Language as UserLanguage;

trait Language
{
  protected $language;

  protected function getLanguageInstance()
  {
    if (!$this->language) {
      $this->language = new UserLanguage();
    }

    return $this->language;
  }

  public function currentLang($tenantId)
  {
    $langCode = session('tenant_frontend_lang');

    if ($langCode) {
      $language = $this->getLanguageInstance()->where('code', $langCode)->where('user_id', $tenantId)->first();
    } else {
      $language = $this->defaultLang($tenantId);
    }

    return $language;
  }

  public function defaultLang($tenantId)
  {

    return  $this->getLanguageInstance()->where('user_id', $tenantId)->where('is_default', 1)->first();
  }
  public function allLangs($tenantId)
  {
    return  $this->getLanguageInstance()->where('user_id', $tenantId)->get();
  }

  public function selectLang($tenantId, $langCode = null)
  {

    $query = $this->getLanguageInstance()->where('user_id', $tenantId);

    if ($langCode !== null) {
      $query->where('code', $langCode);
    }
    return $query->firstOrFail();
  }
}
