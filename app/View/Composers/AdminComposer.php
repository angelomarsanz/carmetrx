<?php

namespace App\View\Composers;


use Illuminate\View\View;
use App\Traits\AdminLanguage;

class AdminComposer
{
  use AdminLanguage;
  public function compose(View $view)
  {
    $currentLang = $this->currentLang();
   
    $view->with('bs', $currentLang->basic_setting);
    $view->with('be', $currentLang->basic_extended);
    $view->with('currentLang', $currentLang);
    $view->with('adminLangs', $this->allLangs());
  }
}
