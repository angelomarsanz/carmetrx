<?php

namespace App\View\Components\Tenant\Frontend\Sections;

use Illuminate\View\Component;

class Agent extends Component
{
    public $agents, $agentInfo;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($agents, $agentInfo)
    {
        $this->agentInfo = $agentInfo;
        $this->agents = $agents;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.tenant.frontend.sections.agent');
    }
}
