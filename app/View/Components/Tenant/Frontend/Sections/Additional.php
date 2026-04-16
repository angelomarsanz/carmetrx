<?php

namespace App\View\Components\Tenant\Frontend\Sections;

use App\Models\User\AdditionalSectionContent;
use Closure;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Additional extends Component
{
    use TenantFrontendLanguage;
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
        $tenantId = getUser()->id;
        $language = $this->currentLang($tenantId);
        $afAboutCon = AdditionalSectionContent::where('addition_section_id', $this->sectionId)
            ->where('language_id', $language->id)
            ->first();
        return view('components.tenant.frontend.sections.additional', compact('afAboutCon'));
    }
}
