<?php

namespace App\View\Components\Tenant\Frontend;

use Illuminate\View\Component;

class Project extends Component
{
    public $project;
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($project)
    {
        $this->project  = $project;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.tenant.frontend.project');
    }
}
