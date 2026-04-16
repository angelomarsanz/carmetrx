<?php

namespace App\View\Components\Tenant\Frontend\Sections;

use App\Models\User\BasicSetting;
use Illuminate\View\Component;

class About extends Component
{
    public $aboutImg, $aboutInfo, $themeVersion;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($aboutImg, $aboutInfo)
    {
        $this->aboutImg = $aboutImg;
        $this->aboutInfo = $aboutInfo;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $this->themeVersion =  BasicSetting::where('user_id', getUser()->id)->pluck('theme_version')->first();
        

        return view('components.tenant.frontend.sections.about');
     
    }
}
