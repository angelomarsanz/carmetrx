<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Traits\AdminLanguage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Mews\Purifier\Facades\Purifier;

class PageController extends Controller
{
    use AdminLanguage;
    public function index(Request $request)
    {
        $lang = $this->selectLang($request->language);
        $lang_id = $lang->id;
        $data['apages'] = Page::where('language_id', $lang_id)->orderBy('id', 'DESC')->get();
        $data['lang_id'] = $lang_id;
        return view('admin.page.index', $data);
    }

    public function create()
    {
        $data['tpages'] = Page::where('language_id', 0)->get();
        return view('admin.page.create', $data);
    }

    public function store(Request $request)
    {
        $slug = make_slug($request->name);



        $rules = [
            'language' => 'required',
            'name' => 'required',
            'title' => 'required',
            'body' => 'required',
            'status' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $page = new Page;
        $page->language_id = $request->language;
        $page->name = $request->name;
        $page->title = $request->title;
        $page->slug = $slug;
        $page->body = Purifier::clean($request->body);
        $page->status = $request->status;
        $page->meta_keywords = $request->meta_keywords;
        $page->meta_description = $request->meta_description;
        $page->save();

        Session::flash('success', __('Added successfully!'));
        return "success";
    }

    public function edit($pageID)
    {
        $data['page'] = Page::findOrFail($pageID);
        return view('admin.page.edit', $data);
    }

    public function update(Request $request)
    {
        $slug = make_slug($request->name);

        $rules = [
            'name' => 'required',
            'title' => 'required',
            'body' => 'required',
            'status' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $pageID = $request->pageid;

        $page = Page::findOrFail($pageID);
        $page->name = $request->name;
        $page->title = $request->title;
        $page->slug = $slug;
        $page->body = Purifier::clean($request->body);
        $page->status = $request->status;
        $page->meta_keywords = $request->meta_keywords;
        $page->meta_description = $request->meta_description;
        $page->save();

        Session::flash('success', __('Updated successfully!'));
        return "success";
    }

    public function delete(Request $request)
    {
        $pageID = $request->pageid;
        $page = Page::findOrFail($pageID);
        $page->delete();
        Session::flash('success', __('Deleted successfully!'));
        return redirect()->back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $page = Page::findOrFail($id);
            $page->delete();
        }

        Session::flash('success', __('Bulk deleted successfully!'));
        return "success";
    }
}
