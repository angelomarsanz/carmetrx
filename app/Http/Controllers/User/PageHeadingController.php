<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\PageHeadingRequest;
use App\Models\User\PageHeading;

use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PageHeadingController extends Controller
{
    use TenantFrontendLanguage;

    public function pageHeadings(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;
        $language = $this->selectLang($userId, $request->language);
        $information['languages'] = $this->allLangs($userId);
        $information['data'] = PageHeading::where([['language_id', $language->id], ['user_id', $userId]])->first();
        return view('user.settings.page-headings', $information);
    }

    public function updatePageHeadings(PageHeadingRequest $request)
    {

        $userId = Auth::guard('web')->user()->id;
        $language = $this->selectLang($userId, $request->language);

        PageHeading::where([['language_id', $language->id], ['user_id', $userId]])->updateOrCreate([
            'user_id' => $userId,
            'language_id' => $language->id,
        ], $request->except(['_token', 'user_id', 'language_id', 'language']));

        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }
}
