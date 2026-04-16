<?php

namespace App\View\Components\Tenant\Frontend\Sections;

use Illuminate\View\Component;

class City extends Component
{
    public $cities, $citySecInfo, $cityBgImg;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($cities, $citySecInfo, $cityBgImg = null)
    {
        $this->cities = $cities;
        $this->citySecInfo = $citySecInfo;
        $this->cityBgImg = $cityBgImg;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.tenant.frontend.sections.city');
    }
}
