<?php

namespace App\View\Components\Tenant\Frontend\Sections;

use App\Models\User\BasicSetting;
use Illuminate\View\Component;

class Categories extends Component
{
    public $property_categories, $catgorySecInfo, $themeVersion;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($propertyCategories, $catgorySecInfo)
    {
        $this->property_categories = $propertyCategories;
        $this->catgorySecInfo = $catgorySecInfo;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $this->themeVersion =  BasicSetting::where('user_id', getUser()->id)->pluck('theme_version')->first();

        return view('components.tenant.frontend.sections.categories');
    }
}
