<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdditionalSection;
use App\Models\AdditionalSectionContent;
use App\Models\BasicSetting;
use Illuminate\Http\Request;
use App\Traits\CustomSection;
use App\Traits\AdminLanguage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AboutAdditionSectionController extends Controller
{
    use AdminLanguage, CustomSection;
    public function index(Request $request)
    {

        if ($request->has('language')) {

            $language = $this->selectLang($request->language);
        } else {
            $language = $this->defaultLang();
        }



        $information['sections'] = AdditionalSection::join('additional_section_contents', 'additional_section_contents.addition_section_id', '=', 'additional_sections.id')
            ->where('language_id', $language->id)
            ->where('page_type', 'about')
            ->select('additional_sections.*', 'additional_section_contents.section_name')
            ->get();

        return view('admin.about-page.additional-section.index', $information);
    }

    public function create(Request $request)
    {

        $information['defaultLang'] = $this->defaultLang();
        $information['adminLangs'] = $this->allLangs();

        $information['page_type'] = 'about';
        $information['all_sections'] = CustomSection::AdminFrontAboutPage();
        return view('admin.about-page.additional-section.create', $information);
    }

    public function store(Request $request)
    {
        $rules = [
            'possition' => 'required',
            'page_type' => 'required',
            'serial_number' => 'required',
        ];
        $languages = $this->allLangs();
        $messages = [];
        foreach ($languages as $language) {
            if ($language->is_default == 1) {
                $rules[$language->code . '_section_name'] = 'required';
                $rules[$language->code . '_content'] = 'required';
                $messages[$language->code . '_section_name.required'] = __('The section name is required for') . ' ' . $language->name . ' ' . __('Language');
                $messages[$language->code . '_content.required'] = __('The section content is required for') . ' ' . $language->name . ' ' . __('Language');
            } else {
                if (!is_null($request[$language->code . '_section_name']) || !is_null($request[$language->code . '_content'])) {
                    $rules[$language->code . '_section_name'] = 'required';
                    $rules[$language->code . '_content'] = 'required';
                    $messages[$language->code . '_section_name.required'] = __('The section name is required for') . ' ' . $language->name . ' ' . __('Language');
                    $messages[$language->code . '_content.required'] =
                        __('The section content is required for') . ' ' . $language->name . ' ' . __('Language.');
                }
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag(),
            ], 400);
        }


        $section = AdditionalSection::create($request->all());

        foreach ($languages as $language) {
            $code = $language->code;
            if ($language->is_default == 1 || $request->filled($code . '_section_name') || $request->filled($code . '_content')) {
                $content = new AdditionalSectionContent();
                $content->language_id = $language->id;
                $content->addition_section_id = $section->id;
                $content->section_name = $request[$code . '_section_name'];
                $content->content = $request[$code . '_content'];
                $content->save();
            }
        }

        $bss = BasicSetting::all();

        foreach ($bss as $key => $bs) {
            $arr = json_decode($bs->about_additional_section_status, true);
            $arr["$section->id"] = "1";

            $bs->about_additional_section_status = json_encode($arr);
            $bs->save();
        }

        Session::flash('success', __('Added successfully!'));

        return  'success';
    }

    public function edit($id, Request $request)
    {
        $information['defaultLang'] = $this->defaultLang();
        $information['adminLangs'] = $this->allLangs();
        $information['section'] = AdditionalSection::where('page_type', 'about')->where('id', $id)->firstOrFail();
        $information['all_sections'] = CustomSection::AdminFrontAboutPage();
        return view('admin.about-page.additional-section.edit', $information);
    }

    public function update(Request $request, $id)
    {

        $rules = [
            'possition' => 'required',
            'page_type' => 'required',
            'serial_number' => 'required',
        ];
        $languages = $this->allLangs();
        $messages = [];
        foreach ($languages as $language) {
            if ($language->is_default == 1) {
                $rules[$language->code . '_section_name'] = 'required';
                $rules[$language->code . '_content'] = 'required';
                $messages[$language->code . '_section_name.required'] = __('The section name is required for') . ' ' . $language->name . ' ' . __('Language');
                $messages[$language->code . '_content.required'] = __('The section content is required for') . ' ' . $language->name . ' ' . __('Language');
            } else {
                $hasExistingContent = AdditionalSectionContent::where('addition_section_id', $id)
                    ->where('language_id', $language->id)
                    ->exists();

                if ($hasExistingContent || !is_null($request[$language->code . '_section_name']) || !is_null($request[$language->code . '_content'])) {
                    $rules[$language->code . '_section_name'] = 'required';
                    $rules[$language->code . '_content'] = 'required';
                    $messages[$language->code . '_section_name.required'] = __('The section name is required for') . ' ' . $language->name . ' ' . __('Language');
                    $messages[$language->code . '_content.required'] =
                        __('The section content is required for') . ' ' . $language->name . ' ' . __('Language');
                }
            }
        }
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag(),
            ], 400);
        }
        $section = AdditionalSection::findOrFail($id);
        $section->possition = $request->possition;
        $section->page_type = $request->page_type;
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
            if ($hasExistingContent || !is_null($request[$language->code . '_section_name']) || !is_null($request[$language->code . '_content'])) {
                // Retrieve the content for the given section and language, or create a new one if it doesn't exist
                $content = AdditionalSectionContent::firstOrNew([
                    'addition_section_id' => $section->id,
                    'language_id' => $language->id,
                ]);
                $content->section_name = $request[$code . '_section_name'];
                $content->content =  $request[$code . '_content'];
                $content->save();
            }
        }
        Session::flash('success', __('Updated successfully!'));

        return   'success';
    }

    public function delete($id)
    {
        $section = AdditionalSection::findOrFail($id);
        $contents = AdditionalSectionContent::where('addition_section_id', $id)->get();
        foreach ($contents as $content) {
            $content->delete();
        }
        $section->delete();
        return redirect()->back()->with('success', __('Deleted successfully!'));
    }

    public function bulkdelete(Request $request)
    {
        $ids = $request->ids;
        foreach ($ids as $id) {
            $page = AdditionalSection::query()->findOrFail($id);
            $contents = AdditionalSectionContent::where('addition_section_id', $id)->get();
            foreach ($contents as $pageContent) {
                $pageContent->delete();
            }
            $page->delete();
        }
        Session::flash('success', __('Deleted successfully!'));
        return 'success';
    }
}
