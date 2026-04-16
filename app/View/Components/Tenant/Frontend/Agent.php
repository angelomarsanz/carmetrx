<?php

namespace App\View\Components\Tenant\Frontend;

use Illuminate\View\Component;

class Agent extends Component
{
    public $agent;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($agent)
    {
        $this->agent = $agent;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.tenant.frontend.agent');
    }
}
