<?php

namespace App\Traits;

use App\Models\Language;

trait AdminLanguage
{
  protected $language;


  protected function getLanguageInstance()
  {
    if (!$this->language) {
      $this->language = new Language();
    }

    return $this->language;
  }

  public function currentLang()
  {
    $langCode = session('admin_lang');

    if ($langCode) {
      $language =  $this->getLanguageInstance()->where('code', $langCode)->first();
    } else {
      $language = $this->getLanguageInstance()->where('is_default', 1)->first();
    }

    return $language;
  }

  public function defaultLang()
  {

    return  $this->getLanguageInstance()->where('is_default', 1)->first();
  }

  public function allLangs()
  {

    return  $this->getLanguageInstance()->get();
  }
  public function selectLang($langCode)
  {

    return  $this->getLanguageInstance()->where('code', $langCode)->firstOrFail();
  }
}
