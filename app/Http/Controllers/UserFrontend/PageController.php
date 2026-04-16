<?php

namespace App\Http\Controllers\UserFrontend;

use App\Http\Controllers\Controller;
use App\Models\User\CustomPage\Page;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Http\Request;

class PageController extends Controller
{
    use TenantFrontendLanguage;
    public function page($username, $slug)
    {
        $misc = new MiscellaneousController();
        $tenantId = getUser()->id;
        $language = $misc->currentLang($tenantId);

        $queryResult['breadcrumb'] = $misc->getBreadcrumb($tenantId);

        $queryResult['pageInfo'] = Page::join('user_page_contents', 'user_pages.id', '=', 'user_page_contents.page_id')
            ->where('user_pages.status', '=', 1)
            ->where('user_page_contents.language_id', '=', $language->id)
            ->where('user_page_contents.slug', '=', $slug)
            ->firstOrFail();

        return view('tenant_frontend.custom-page', $queryResult);
    }
}
