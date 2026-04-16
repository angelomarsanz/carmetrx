<?php

namespace App\Http\Controllers\User\Journal;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Http\Requests\Blog\StoreRequest;
use App\Http\Requests\Blog\UpdateRequest;
use App\Models\User\Journal\Blog;
use App\Models\User\Journal\BlogCategory;
use App\Models\User\Journal\BlogInformation;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mews\Purifier\Facades\Purifier;

class BlogController extends Controller
{
    use TenantFrontendLanguage;
    /**
     * Display a listing of the resource.
     *
     * @return
     */
    public function index(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        if ($request->has('language')) {
            $language = $this->selectLang($tenantId, $request->language);
        } else {
            $language = $this->defaultLang($tenantId);
        }

        $information['blogs'] = Blog::where('user_id', $tenantId)

            ->orderBy('serial_number', 'asc')
            ->paginate(10)
            ->map(function ($item) use ($language) {
                $content = $item->information($language->id);
                $catContent = $item->categoryContent($language->id);
                $item->title = optional($content)->title;
                $item->author = optional($content)->author;
                $item->category_name = optional($catContent)->name;
                return $item;
            });
        return view('user.journal.blog.index', $information);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return
     */
    public function create(Request $request)
    {
        // get all the languages from db
        $tenantId = Auth::guard('web')->user()->id;

        if ($request->has('language')) {
            $language = $this->selectLang($tenantId, $request->language);
        } else {
            $language = $this->defaultLang($tenantId);
        }

        // $language =  $this->defaultLang($tenantId);
        $information['tenantLangs'] = $this->allLangs($tenantId);
        $information['defaultLang'] = $language;

        $information['categories'] = BlogCategory::getCategories($tenantId, $language->id);

        return view('user.journal.blog.create', $information);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return
     */
    public function store(StoreRequest $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        // store image in storage
        $imgName = UploadFile::store(Constant::WEBSITE_BLOG_IMAGE, $request->file('image'));

        // store data in db
        $blog = Blog::create([
            'image' => $imgName,
            'user_id' => $tenantId,
            'category_id' => $request->category_id,
            'serial_number' => $request->serial_number,
        ]);


        $languages = $this->allLangs($tenantId);
        foreach ($languages as $language) {
            if ($request->filled($language->code . '_title') || $request->filled($language->code . '_author') || $request->filled($language->code . '_content') || $request->filled($language->code . '_meta_keywords') || $request->filled($language->code . '_meta_description')) {
                $blogInformation = new BlogInformation();
                $blogInformation->language_id = $language->id;
                $blogInformation->user_id = $tenantId;
                $blogInformation->blog_id = $blog->id;
                $blogInformation->title = $request[$language->code . '_title'];
                $blogInformation->slug = $request[$language->code . '_title'];
                $blogInformation->author = $request[$language->code . '_author'];
                $blogInformation->content = $request[$language->code . '_content'];
                $blogInformation->meta_keywords = $request[$language->code . '_meta_keywords'];
                $blogInformation->meta_description = $request[$language->code . '_meta_description'];
                $blogInformation->save();
            }
        }

        session()->flash('success', __('Added successfully!'));
        return "success";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return
     */
    public function edit($id)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $information['tenantLangs']  = $this->allLangs($tenantId);
        $information['blog'] = Blog::query()->where('user_id', $tenantId)->findOrFail($id);  // get all the languages from db
        $language = $this->defaultLang($tenantId);
        $information['defaultLang'] = $language;

        $information['categories'] = BlogCategory::getCategories($tenantId, $language->id);

        return view('user.journal.blog.edit', $information);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return
     */
    public function update(UpdateRequest $request, $id)
    {

        $userId = Auth::guard('web')->user()->id;
        $blog = Blog::query()->where('user_id', $userId)->findOrFail($id);

        // store new image in storage
        if ($request->hasFile('image')) {
            $imgName = UploadFile::update(Constant::WEBSITE_BLOG_IMAGE, $request->file('image'), $blog->image);
        }
        try {

            // update data in db
            $blog->update($request->except('image') + [
                'image' => $request->hasFile('image') ? $imgName : $blog->image,
                'category_id' => $request->category_id,
            ]);


            $languages = $this->allLangs($userId);
            foreach ($languages as $language) {
                if ($request->filled($language->code . '_title') || $request->filled($language->code . '_author') || $request->filled($language->code . '_content') || $request->filled($language->code . '_meta_keywords') || $request->filled($language->code . '_meta_description')) {

                    BlogInformation::query()->updateOrCreate([
                        'blog_id' => $id,
                        'user_id' => $userId,
                        'language_id' => $language->id,

                    ], [
                        'blog_category_id' => $request[$language->code . '_category_id'],
                        'title' => $request[$language->code . '_title'],
                        'slug' => $request[$language->code . '_title'],
                        'author' => $request[$language->code . '_author'],
                        'content' => $request[$language->code . '_content'],
                        'language_id' => $language->id,
                        'meta_keywords' => $request[$language->code . '_meta_keywords'],
                        'meta_description' => $request[$language->code . '_meta_description']
                    ]);
                }
            }

            session()->flash('success', __('Updated successfully!'));
        } catch (Exception $e) {
            session()->flash('warning', __('Something went wrong!'));
        }
        return "success";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return
     */
    public function destroy($id)
    {
        $userId = Auth::guard('web')->user()->id;
        $blog = Blog::query()->where('user_id',  $userId)->findOrFail($id);
        // first, delete the image
        @unlink(public_path(Constant::WEBSITE_BLOG_IMAGE) . '/' . $blog->image);
        $blogInformations = BlogInformation::query()->where('blog_id', $blog->id)->where('user_id', $userId)->get();
        foreach ($blogInformations as $blogInformation) {
            $blogInformation->delete();
        }
        $blog->delete();
        return redirect()->back()->with('success', __('Deleted successfully!'));
    }

    /**
     * Remove the selected or all resources from storage.
     *
     * @param Request $request
     * @return
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        foreach ($ids as $id) {
            $blog = Blog::query()->where('user_id', Auth::guard('web')->user()->id)->findOrFail($id);
            // first, delete the image 
            @unlink(public_path(Constant::WEBSITE_BLOG_IMAGE) . '/' . $blog->image);
            $blogInformations = BlogInformation::query()->where('blog_id', $blog->id)->where('user_id', $userId)->get();
            foreach ($blogInformations as $blogInformation) {
                $blogInformation->delete();
            }
            $blog->delete();
        }
        session()->flash('success', __('Deleted successfully!'));
        return "success";
    }
}
