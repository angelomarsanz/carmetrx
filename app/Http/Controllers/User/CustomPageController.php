<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\CustomPage\Page;
use App\Models\User\CustomPage\PageContent;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Mews\Purifier\Facades\Purifier;

class CustomPageController extends Controller
{
    use TenantFrontendLanguage;
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        if ($request->has('language')) {

            $language = $this->selectLang($tenantId, $request->language);
        } else {
            $language = $this->defaultLang($tenantId);
        }
        $page = Page::where('user_pages.user_id', $tenantId)
            ->orderByDesc('id')
            ->get();
        $page->map(function ($item) use ($language) {
            $itemContent = $item->contents()->where('language_id', $language->id)->select('title')->first();
            $item['title'] = $itemContent?->title;
        });

        $information['pages']  = $page;
        return view('user.custom-page.index', $information);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $tenantId = Auth::guard('web')->user()->id;
        $information['tenantLangs'] = $this->allLangs($tenantId);
        $information['defaultLang'] = $this->defaultLang($tenantId);
        return view('user.custom-page.create', $information);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return
     */
    public function store(Request $request)
    {
        $rules = ['status' => 'required'];
        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);

        $messages = [];


        $defaulLang = $this->defaultLang($tenantId);
        $rules[$defaulLang->code . '_title'] = [
            'required',
            'max:255',
            Rule::unique('user_page_contents', 'title')->where('language_id', $defaulLang->id)->where('user_id', $tenantId)
        ];
        $rules[$defaulLang->code . '_content'] = 'required';

        foreach ($languages as $language) {
        
            if ($request->filled($language->code . '_title') || $request->filled($language->code . '_content')) {
                $slug = slug_create($request[$language->code . '_title']);
                $rules[$language->code . '_title'] = [
                    'required',
                    'max:255',
                    function ($attribute, $value, $fail) use ($slug, $language) {
                        $pcs = PageContent::where('language_id', $language->id)->where('user_id', Auth::guard('web')->user()->id)->get();
                        foreach ($pcs as $key => $pc) {
                            if (strtolower($slug) == strtolower($pc->slug)) {
                                $fail(__('The title field must be unique for') . ' ' . $language->name . ' ' . __('language'));
                            }
                        }
                    }
                ];
                $rules[$language->code . '_content'] = 'required';
            }
            $messages[$language->code . '_title.required'] = __('The title field is required for') . ' ' . $language->name . ' ' . __('language');

            $messages[$language->code . '_title.unique'] = __('The title field must be unique for') . ' ' . $language->name . ' ' . __('language');

            $messages[$language->code . '_content.required'] = __('The content field is required for') . ' ' . $language->name . ' ' . __('language');
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }
        DB::beginTransaction();
        try {
            $page = new Page();
            $page->user_id = Auth::guard('web')->user()->id;
            $page->status = $request->status;
            $page->save();
            foreach ($languages as $language) {
                if ($request->filled($language->code . '_title') || $request->filled($language->code . '_content')) {
                    $pageContent = new PageContent();
                    $pageContent->language_id = $language->id;
                    $pageContent->user_id = $tenantId;
                    $pageContent->page_id = $page->id;
                    $pageContent->title = $request[$language->code . '_title'];
                    $pageContent->slug = $request[$language->code . '_title'];
                    $pageContent->content = $request[$language->code . '_content'];
                    $pageContent->meta_keywords = $request[$language->code . '_meta_keywords'];
                    $pageContent->meta_description = $request[$language->code . '_meta_description'];
                    $pageContent->save();
                }
            }
            DB::commit();
            session()->flash('success', __('Added successfully!'));
        } catch (\Exception $e) {
            session()->flash('warning', __('Something went wrong!'));
            DB::rollBack();
        }
        return "success";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);
        $information['defaultLang'] =  $this->defaultLang($tenantId);
        $information['tenantLangs'] =  $languages;
        $information['page'] = Page::query()->where('user_id', Auth::guard('web')->user()->id)->findOrFail($id);
        return view('user.custom-page.edit', $information);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return
     */
    public function update(Request $request, int $id)
    {

        $rules = ['status' => 'required'];

        $tenantId = Auth::guard('web')->user()->id;
        $languages = $this->allLangs($tenantId);

        $messages = [];
        $defaulLang = $this->defaultLang($tenantId);
        $dslug = slug_create($request[$defaulLang->code . '_title']);
        $rules[$defaulLang->code . '_title'] = [
            'required',
            'max:255',
            function ($attribute, $value, $fail) use ($dslug, $id, $defaulLang) {
                $pcs = PageContent::where('page_id', '<>', $id)->where('language_id', $defaulLang->id)->where('user_id', Auth::guard('web')->user()->id)->get();
                foreach ($pcs as $key => $pc) {
                    if (strtolower($dslug) == strtolower($pc->slug)) {
                        $fail(__('The title field must be unique for') . ' ' . $defaulLang->name . ' ' . __('language'));
                    }
                }
            }
        ];
        $rules[$defaulLang->code . '_content'] = 'required';
       
        foreach ($languages as $language) {
            
            if ($request->filled($language->code . '_title') || $request->filled($language->code . '_content')) {
                $slug = slug_create($request[$language->code . '_title']);
                $rules[$language->code . '_title'] = [
                    'required',
                    'max:255',
                    function ($attribute, $value, $fail) use ($slug, $id, $language) {
                        $pcs = PageContent::where('page_id', '<>', $id)->where('language_id', $language->id)->where('user_id', Auth::guard('web')->user()->id)->get();
                        foreach ($pcs as $key => $pc) {
                            if (strtolower($slug) == strtolower($pc->slug)) {
                                $fail(__('The title field must be unique for') . ' ' . $language->name . ' ' . __('language'));
                            }
                        }
                    }
                ];

                $rules[$language->code . '_content'] = 'required';
            }
                $messages[$language->code . '_title.required'] = __('The title field is required for') . ' ' . $language->name . ' ' . __('language');


                $messages[$language->code . '_title.unique'] = __('The title field must be unique for') . ' ' . $language->name . ' ' . __('language');

                $messages[$language->code . '_content.required'] = __('The content field is required for') . ' ' . $language->name . ' ' . __('language');
            
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }

        $page = Page::query()->where('user_id', $tenantId)->findOrFail($id);
        $page->update(['status' => $request->status]);

        foreach ($languages as $language) {

            if ($request->filled($language->code . '_title') || $request->filled($language->code . '_content')) {
                PageContent::query()->updateOrCreate([
                    'page_id' => $id,
                    'user_id' => $tenantId,
                    'language_id' => $language->id,
                ], [
                    'title' => $request[$language->code . '_title'],
                    'slug' => make_slug($request[$language->code . '_title']),
                    'content' => Purifier::clean($request[$language->code . '_content']),
                    'user_id' => $tenantId,
                    'language_id' => $language->id,
                    'meta_keywords' => $request[$language->code . '_meta_keywords'],
                    'meta_description' => $request[$language->code . '_meta_description']
                ]);
            }
        }

        session()->flash('success', __('Updated successfully!'));
        return "success";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $page = Page::query()->where('user_id', Auth::guard('web')->user()->id)->findOrFail($id);
        $page->contents()->delete();
        $page->delete();
        return redirect()->back()->with('success', __('Deleted successfully!'));
    }

    /**
     * Remove the selected or all resources from storage.
     *
     * @param Request $request
     * @return string
     */
    public function bulkDestroy(Request $request): string
    {
        $ids = $request->ids;
        foreach ($ids as $id) {
            $page = Page::query()->where('user_id', Auth::guard('web')->user()->id)->findOrFail($id);
            $page->contents()->delete();
            $page->delete();
        }
        session()->flash('success', __('Deleted successfully!'));
        return "success";
    }
}
