<?php

namespace App\View\Components\Tenant\Frontend\Sections;

use Illuminate\View\Component;

class WhyChooseUs extends Component
{
    public $whyChooseUsInfo, $whyChooseUsImg;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($whyChooseUsInfo, $whyChooseUsImg)
    {
        $this->whyChooseUsImg = $whyChooseUsImg;
        $this->whyChooseUsInfo = $whyChooseUsInfo;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.tenant.frontend.sections.why-choose-us');
    }
}
