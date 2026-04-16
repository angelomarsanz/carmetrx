<?php

namespace App\View\Components\Tenant\Frontend;

use Illuminate\View\Component;

class Property extends Component
{
    public $property;

    public  $animation;
    public  $adminLang;

    protected $except = [];
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($property, $animate = true, $adminLang = null)
    {

        $this->property = $property;
        $this->animation = $animate;
        $this->adminLang = $adminLang;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.tenant.frontend.property');
    }
}
