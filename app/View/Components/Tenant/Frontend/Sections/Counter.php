<?php

namespace App\View\Components\Tenant\Frontend\Sections;

use App\Models\User\BasicSetting;
use Illuminate\View\Component;

class Counter extends Component
{
    public $counters, $counterSectionImage;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($counters, $counterSectionImage = null)
    {
        $this->counters = $counters;
        $this->counterSectionImage = $counterSectionImage;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $themeVersion =  BasicSetting::where('user_id', getUser()->id)->pluck('theme_version')->first();
        return view('components.tenant.frontend.sections.counter', compact('themeVersion'));
    }
}
