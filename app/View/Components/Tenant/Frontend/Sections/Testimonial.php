<?php

namespace App\View\Components\Tenant\Frontend\Sections;

use App\Models\User\BasicSetting;
use Illuminate\View\Component;

class Testimonial extends Component
{
    public $testimonials, $testimonialSecInfo, $testimonialSecImage;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($testimonials, $testimonialSecInfo, $testimonialSecImage = null)
    {
        $this->testimonials = $testimonials;
        $this->testimonialSecInfo = $testimonialSecInfo;
        $this->testimonialSecImage = $testimonialSecImage;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $themeVersion =  BasicSetting::where('user_id', getUser()->id)->pluck('theme_version')->first();
        return view('components.tenant.frontend.sections.testimonial',compact('themeVersion'));
    }
}
