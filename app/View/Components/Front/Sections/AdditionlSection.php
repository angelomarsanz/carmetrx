<?php

namespace App\View\Components\Front\Sections;

use App\Models\AdditionalSectionContent;
use Closure;
use App\Traits\FrontendLanguage;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdditionlSection extends Component
{
    use FrontendLanguage;
    public $sectionId;
    /**
     * Create a new component instance.
     */
    public function __construct($sectionId)
    {
        $this->sectionId = $sectionId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        $language = $this->currentLang();
        $content = AdditionalSectionContent::where('addition_section_id', $this->sectionId)
            ->where('language_id', $language->id)
            ->first();
        return view('components.front.sections.additionl-section',compact('content'));
    }
}
