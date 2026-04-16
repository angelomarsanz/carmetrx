<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;
use App\Traits\AdminLanguage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    use AdminLanguage;
    public function index(Request $request)
    {
        $lang = $this->selectLang($request->language);

        $lang_id = $lang->id;
        $data['faqs'] = Faq::where('language_id', $lang_id)->orderBy('serial_number', 'asc')->get();

        $data['lang_id'] = $lang_id;

        return view('admin.faq.index', $data);
    }

    public function store(Request $request)
    {


        $rules = [
            'language' => 'required',
            'question' => 'required|max:255',
            'answer' => 'required',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $faq = new Faq;
        $faq->language_id = $request->language;
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->serial_number = $request->serial_number;
        $faq->save();

        Session::flash('success', __('Added successfully!'));
        return "success";
    }

    public function update(Request $request)
    {
        $rules = [
            'question' => 'required|max:255',
            'answer' => 'required',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $faq = Faq::findOrFail($request->faq_id);
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->serial_number = $request->serial_number;
        $faq->save();

        Session::flash('success', __('Updated successfully!'));
        return "success";
    }

    public function delete(Request $request)
    {

        $faq = Faq::findOrFail($request->faq_id);
        $faq->delete();

        Session::flash('success', __('Deleted successfully!'));
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $faq = Faq::findOrFail($id);
            $faq->delete();
        }

        Session::flash('success', __('Bulk deleted successfully!'));
        return "success";
    }
}
