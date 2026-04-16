<?php

namespace App\View\Composers;

use App\Models\Language;
use Illuminate\View\View;
use App\Models\Menu;
use App\Models\Social;
use App\Traits\FrontendLanguage;

class GlobalComposer
{
  use FrontendLanguage;
  public function compose(View $view)
  {
    $currentLang = $this->currentLang();

    $view->with('bs', $currentLang->basic_setting);
    $view->with('be', $currentLang->basic_extended);
  }
}
