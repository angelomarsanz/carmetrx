<?php

namespace App\View\Composers;

use App\Models\Language;
use Illuminate\View\View;
use App\Models\Menu;
use App\Models\Social;
use App\Traits\FrontendLanguage;

class FrontendComposer
{
  use FrontendLanguage;
  public function compose(View $view)
  {

    $currentLang = $this->currentLang();
    $menus = Menu::where('language_id', $currentLang->id)->first()->menus ?? json_encode([]);
    $rtl = $currentLang->rtl ? 1 : 0;

    $view->with('bs', $currentLang->basic_setting);
    $view->with('be', $currentLang->basic_extended);
    $view->with('currentLang', $currentLang);
    $view->with('menus', $menus);
    $view->with('rtl', $rtl);
    $view->with('socials', Social::orderBy('serial_number', 'ASC')->get());
    $view->with('langs', $this->allLangs());
  }
}
