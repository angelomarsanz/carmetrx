<?php

namespace App\View\Components\Tenant\Frontend\Sections;

use App\Models\User\BasicSetting;
use Illuminate\View\Component;

class WorkSteps extends Component
{
    public $workStepsSecInfo, $steps, $workStepsSecImg, $themeVersion;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($workStepsSecInfo, $steps, $workStepsSecImg = null)
    {
        $this->workStepsSecInfo = $workStepsSecInfo;
        $this->steps = $steps;
        $this->workStepsSecImg = $workStepsSecImg;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $this->themeVersion =  BasicSetting::where('user_id', getUser()->id)->pluck('theme_version')->first();

        return view('components.tenant.frontend.sections.work-steps');
    }
}
