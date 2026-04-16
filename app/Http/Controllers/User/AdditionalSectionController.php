<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\AdditionalSection;
use App\Models\User\AdditionalSectionContent;
use App\Models\User\BasicSetting;
use Illuminate\Http\Request;
use App\Traits\CustomSection;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdditionalSectionController extends Controller
{
    use TenantFrontendLanguage, CustomSection;
    public function index(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        if ($request->has('language')) {

            $language = $this->selectLang($tenantId, $request->language);
        } else {
            $language = $this->defaultLang($tenantId);
        }

        $information['sections'] = AdditionalSection::join('user_additional_section_contents', 'user_additional_section_contents.addition_section_id', '=', 'user_additional_sections.id')
            ->where('user_additional_section_contents.language_id', $language->id)
            ->where('user_additional_sections.page_type', 'home')
            ->select('user_additional_sections.*', 'user_additional_section_contents.section_name')
            ->get();
        return view('user.home-page.additional-sections.index', $information);
    }

    public function create(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        if ($request->has('language')) {

            $language = $this->selectLang($tenantId, $request->language);
        } else {
            $language = $this->defaultLang($tenantId);
        }

        $settings = BasicSetting::where('user_id', $tenantId)->select('theme_version')->first();

        $information['language']  = $language;
        $information['defaultLang']  = $this->defaultLang($tenantId);
        $information['tenantLangs'] = $this->allLangs($tenantId);
        $information['page_type'] = 'home';

        if ($settings->theme_version == 1) {
            $information['all_sections'] = CustomSection::TenantFrontThemeOne();
        }
        if ($settings->theme_version == 2) {
            $information['all_sections'] = CustomSection::TenantFrontThemeTwo();
        }
        if ($settings->theme_version == 3) {
            $information['all_sections'] = CustomSection::TenantFrontThemeThree();
        }

        return view('user.home-page.additional-sections.create', $information);
    }

    public function store(Request $request)
    {
        $rules = [
            'possition'     => 'required',
            'page_type'     => 'required',
            'serial_number' => 'required',
        ];
        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);
        $messages  = [];

        $defaulLang = $this->defaultLang($tenantId);
        $rules[$defaulLang->code . '_section_name'] = 'required';
        $rules[$defaulLang->code . '_content'] = 'required';

        foreach ($languages as $language) {
            if ($request->input($language->code . '_section_name') ||  $request->input($language->code . '_content')) {
                $rules[$language->code . '_section_name'] = 'required';
                $rules[$language->code . '_content'] = 'required';
            }
            $messages[$language->code . '_section_name.required'] = __('The section title is required for') . ' ' . $language->name . ' ' . __('language');
            $messages[$language->code . '_content.required']  = __('The section content is required for') . ' ' . $language->name . ' ' . __('language');
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag(),
            ], 400);
        }


        $in            = $request->all();
        $in['user_id'] = Auth::guard('web')->user()->id;
        $section       = AdditionalSection::create($in);

        foreach ($languages as $language) {
            $code = $language->code;
            if ($request->input($language->code . '_section_name') ||  $request->input($language->code . '_content')) {
                $content                      = new AdditionalSectionContent();
                $content->language_id         = $language->id;
                $content->addition_section_id = $section->id;
                $content->section_name        = $request[$code . '_section_name'];
                $content->content             =  $request[$code . '_content'];
                $content->save();
            }
        }

        $bs = BasicSetting::where('user_id', $tenantId)->first();

        $arr                 = json_decode($bs->additional_section_status, true);
        $arr["$section->id"] = "1";

        $bs->additional_section_status = json_encode($arr);
        $bs->save();

        Session::flash('success', __('Added successfully!'));
        return 'success';
    }

    public function edit($id, Request $request)
    {

        $tenantId = Auth::guard('web')->user()->id;
        $settings = BasicSetting::where('user_id', $tenantId)->select('theme_version')->first();
        $information['tenantLangs'] = $this->allLangs($tenantId);
        $information['defaultLang']  = $this->defaultLang($tenantId);
        $information['section']   = AdditionalSection::where([['page_type', 'home'], ['user_id', $tenantId], ['id', $id]])->firstOrFail();

        if ($settings->theme_version == 1) {
            $information['all_sections'] = CustomSection::TenantFrontThemeOne();
        }
        if ($settings->theme_version == 2) {
            $information['all_sections'] = CustomSection::TenantFrontThemeTwo();
        }
        if ($settings->theme_version == 3) {
            $information['all_sections'] = CustomSection::TenantFrontThemeThree();
        }

        return view('user.home-page.additional-sections.edit', $information);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'possition'     => 'required',
            'page_type'     => 'required',
            'serial_number' => 'required',
        ];
        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);
        $defaulLang = $this->defaultLang($tenantId);
        $messages  = [];

        $rules[$defaulLang->code . '_section_name'] = 'required|max:255';
        $rules[$defaulLang->code . '_content'] = 'required';

        foreach ($languages as $language) {
            $hasExistingContent = AdditionalSectionContent::where('addition_section_id', $id)
                ->where('language_id', $language->id)
                ->exists();
            if (
                $hasExistingContent ||
                $request->input($language->code . '_section_name') ||
                $request->input($language->code . '_content')
            ) {
                $rules[$language->code . '_section_name'] = 'required';
                $rules[$language->code . '_content'] = 'required';
            }

            $messages[$language->code . '_section_name.required'] = __('The section title is required for') . ' ' . $language->name . ' ' . __('language');
            $messages[$language->code . '_content.required'] = __('The section content is required for') . ' ' . $language->name . ' ' . __('language');
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag(),
            ], 400);
        }

        $section = AdditionalSection::where([
            ['id', $id],
            ['user_id', Auth::guard('web')->user()->id],
        ])->first();
        $section->possition     = $request->possition;
        $section->page_type     = $request->page_type;
        $section->serial_number = $request->serial_number;
        $section->save();

        foreach ($languages as $language) {
            $hasExistingContent = AdditionalSectionContent::where('addition_section_id', $id)
                ->where('language_id', $language->id)
                ->exists();
            $content = AdditionalSectionContent::where('addition_section_id', $id)->where('language_id', $language->id)->first();
            if (empty($content)) {
                $content = new AdditionalSectionContent();
            }
            $code = $language->code;
            if ($hasExistingContent || $request->filled($code . '_section_name') || $request->filled($code . '_content')) {
                // Retrieve the content for the given section and language, or create a new one if it doesn't exist
                $content = AdditionalSectionContent::firstOrNew([
                    'addition_section_id' => $section->id,
                    'language_id'         => $language->id,
                ]);
                $content->section_name = $request[$code . '_section_name'];
                $content->content      = $request[$code . '_content'];
                $content->save();
            }
        }

        Session::flash('success', __('Updated successfully!'));

        return 'success';
    }

    public function delete($id)
    {
        $section  = AdditionalSection::where([['id', $id], ['user_id', Auth::guard('web')->user()->id]])->first();
        $contents = AdditionalSectionContent::where('addition_section_id', $id)->get();
        foreach ($contents as $content) {
            $content->delete();
        }
        $section->delete();
        return redirect()->back()->with('success', __('Delete successfully!'));
    }

    public function bulkdelete(Request $request)
    {
        $ids = $request->ids;
        foreach ($ids as $id) {
            $section = AdditionalSection::where([['id', $id], ['user_id', Auth::guard('web')->user()->id]])->first();

            $contents = AdditionalSectionContent::where('addition_section_id', $id)->get();

            foreach ($contents as $pageContent) {
                $pageContent->delete();
            }

            $section->delete();
        }
        Session::flash('success', __('Deleted successfully!'));
        return "success";
    }
}
