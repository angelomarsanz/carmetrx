<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\User\Menu;
use App\Constants\Constant;
use Illuminate\Http\Request;
use App\Models\User\Language;
use App\Http\Helpers\Uploader;
use Illuminate\Validation\Rule;
use App\Models\User\PageHeading;
use App\Models\User\BasicSetting;
use App\Models\User\Journal\Blog;
use Illuminate\Support\Facades\DB;
use App\Models\User\UserPermission;
use App\Http\Controllers\Controller;
use App\Models\User\CustomPage\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Language as AdminLanguage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Helpers\UserPermissionHelper;
use App\Models\User\HomePage\SectionTitle;
use App\Models\User\CustomPage\PageContent;
use App\Models\User\Journal\BlogInformation;

class LanguageController extends Controller
{
    public function changeDashboardLanguage(Request $request)
    {
        $adminLang = AdminLanguage::where('code', $request->language)->first();
        if ($adminLang) {
            session()->put('tenant_dashboard_lang', $adminLang->code);
        } else {
            $adminLang = AdminLanguage::where('default', 1)->first();
            session()->put('tenant_dashboard_lang', $adminLang->code);
        }
        return redirect()->back();
    }

    public function index()
    {
        $userId = Auth::guard('web')->user()->id;
        $data['languages'] = Language::query()->where('user_id', $userId)->get();
        $data['tenantId'] = $userId;
        return view('user.language.index', $data);
    }

    public function store(Request $request)
    {
        $tenantID = Auth::guard('web')->user()->id;
        $rules = [
            'name' => 'required|max:255',
            'code' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($tenantID) {
                    $language = Language::where([
                        ['code', $value],
                        ['user_id', $tenantID]
                    ])->get();
                    if ($language->count() > 0) {
                        $fail(':attribute already taken');
                    }
                },
            ],
            'direction' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $userFrontkeywords =  file_get_contents(resource_path('lang/') . 'user_frontend_default.json');
        DB::transaction(function () use ($request, $userFrontkeywords, $tenantID) {
            try {

                $langData['name'] = $request->name;
                $langData['code'] = strtolower($request->code);
                $langData['is_default'] = 0;
                $langData['rtl'] = $request->direction;
                $langData['keywords'] =  $userFrontkeywords;

                $language = new Language();
                $userLang = $language->store($tenantID, $langData);

                // ==== store default section title under language ====
                $section = new SectionTitle();
                $section->updateOrCreateSectionTitle($tenantID, $userLang->id);

                // ==== store default menus under language ====
                $defaultMenus =  config('defaults.menus', []);
                $tenantMenu = new Menu();
                $tenantMenu->store($tenantID, $userLang->id, $defaultMenus);
            } catch (\Exception $e) {
                Session::flash('success', __('Something went wrong!'));
                return "success";
            }
        });

        Session::flash('success', __('Added successfully!'));
        return "success";
    }



    public function edit($id)
    {
        if ($id > 0) {
            $data['language'] = Language::where('user_id', Auth::guard('web')->user()->id)->where('id', $id)->firstOrFail();
        }
        $data['id'] = $id;
        return view('user.language.edit', $data);
    }


    public function update(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;
        $language = Language::query()
            ->where('user_id', $userId)
            ->where('id', $request->language_id)
            ->firstOrFail();

        $rules = [
            'name' => 'required|max:255',
            'code' => [
                'required',
                'max:255',
                Rule::unique('user_languages')->where('user_id', $userId)->ignore($language->id),
            ],
            'direction' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $language->name = $request->name;
        $language->code = strtolower($request->code);
        $language->rtl = $request->direction;
        $language->save();

        Session::flash('success', __('Updated successfully!'));
        return "success";
    }


    public function editKeyword($id)
    {
        $data['la'] = Language::where('user_id', Auth::guard('web')->user()->id)->where('id', $id)->firstOrFail();
        $data['keywords'] = json_decode($data['la']->keywords, true);

        return view('user.language.edit-keyword', $data);
    }

    public function updateKeyword(Request $request, $id)
    {
        $lang = Language::query()->where('user_id', Auth::guard('web')->user()->id)->where('id', $id)->firstOrFail();
        $lang->keywords = json_encode($request->keys);
        $lang->save();
        return back()->with('success', __('Updated successfully!'));
    }


    public function delete($id)
    {
        $language = Language::query()
            ->where('user_id', Auth::guard('web')->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($language->is_default == 1) {
            return back()->with('warning', __('Default language cannot be deleted!'));
        } else {
            try {
                $language->deteleLanguage();
                Session::flash('success', __('Deleted successfully!'));
            } catch (Exception $e) {
                Session::flash('warning', $e->getMessage());
            }
        }
        return redirect()->back();
    }

    public function default(Request $request, $id)
    {
        Language::query()->where('is_default', 1)->where('user_id', Auth::guard('web')->user()->id)->update(['is_default' => 0]);
        $lang = Language::query()->where('user_id', Auth::guard('web')->user()->id)->find($id);
        $lang->is_default = 1;
        $lang->save();
        return back()->with('success', $lang->name . ' ' . __('language is set as default'));
    }

    public function rtlcheck($langid)
    {
        if ($langid > 0) {
            $lang = Language::query()
                ->where('user_id', Auth::guard('web')->user()->id)
                ->where('id', $langid)
                ->firstOrFail();
        } else {
            return 0;
        }
        return $lang->rtl;
    }

    public function addKeyword(Request $request, $id)
    {
        $request->validate([
            'keyword' => 'required'
        ]);

        $language = Language::find($id);

        $keywords =  json_decode($language->keywords, true);

        $keywords[preg_replace('/\s+/', '_', strtolower($request->keyword))] = ucwords($request->keyword);
        $language->keywords = json_encode($keywords);
        $language->save();

        Session::flash('success', 'A new keyword add successfully for ' . $language->name . ' language!');
        return 'success';
    }
}
