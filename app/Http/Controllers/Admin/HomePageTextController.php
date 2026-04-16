<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BasicSetting as BS;
use App\Models\Language;
use App\Traits\AdminLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomePageTextController extends Controller
{
    use AdminLanguage;
    public function index(Request $request)
    {
        if (empty($request->language)) {
            $data['lang_id'] = $this->defaultLang();
        } else {
            $lang = $this->selectLang($request->language);
            $data['lang_id'] = $lang->id;
        }
        $data['abs'] = $lang->basic_setting;
        return view('admin.home.home-page-text', $data);
    }

    public function update(Request $request, $langid)
    {
        $bs = BS::where('language_id', $langid)->firstOrFail();
        foreach ($request->types as $key => $type) {
            $bs->$type = $request[$type];
        }
        $bs->save();
        Session::flash('success', __('Updated successfully!'));
        return "success";
    }
}
