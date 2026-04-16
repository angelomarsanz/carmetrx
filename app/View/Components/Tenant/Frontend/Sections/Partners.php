<?php

namespace App\View\Components\Tenant\Frontend\Sections;

use Illuminate\View\Component;

class Partners extends Component
{
    public $partners;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($partners)
    {
        $this->partners = $partners;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.tenant.frontend.sections.partners');
    }
}
