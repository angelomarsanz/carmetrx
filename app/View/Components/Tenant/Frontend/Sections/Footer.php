<?php

namespace App\View\Components\Tenant\Frontend\Sections;

use App\Models\User\Journal\Blog;
use App\Models\User\Social;
use App\Traits\Tenant\Frontend\Language as TenantLanguage;
use Illuminate\View\Component;

class Footer extends Component
{
    use TenantLanguage;
    public  $basicInfo;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($basicInfo)
    {
        $this->basicInfo = $basicInfo;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $tenantId = getUser()->id;
        $currentLang = $this->currentLang($tenantId);

        $recent_blogs =  Blog::recentBlogs($tenantId, $currentLang->id, 2);

        $socialMediaInfos = Social::where('user_id', $tenantId)->orderBy('serial_number', 'asc')->get();
        $footerInfo = $currentLang->footerText()->first();
        $quickLinkInfos = $currentLang->footerQuickLinks()->orderBy('serial_number', 'asc')->get();

        return view('components.tenant.frontend.sections.footer', compact('recent_blogs', 'socialMediaInfos', 'footerInfo', 'quickLinkInfos'));
    }
}
