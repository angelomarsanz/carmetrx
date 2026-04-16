<?php

namespace App\Http\Controllers\UserFrontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Tenant\Frontend\PageHeadings;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;


class FaqController extends Controller
{
    use TenantFrontendLanguage, PageHeadings;
    public function faq()
    {
        $tenantId = getUser()->id;
        $misc = new MiscellaneousController();

        $language = $this->currentLang($tenantId);

        $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_faq', 'meta_description_faq')->first();

        $queryResult['pageHeading'] = $this->pageHeading($tenantId);

        $queryResult['breadcrumb'] = $misc->getBreadcrumb($tenantId);

        $queryResult['faqs'] = $language->faqs($tenantId)->orderBy('serial_number', 'asc')->get();

        return view('tenant_frontend.faq', $queryResult);
    }
}
