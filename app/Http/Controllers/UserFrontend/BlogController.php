<?php

namespace App\Http\Controllers\UserFrontend;

use App\Http\Controllers\Controller;
use App\Models\User\BasicSetting;
use App\Models\User\Journal\Blog;
use App\Models\User\Journal\BlogCategory;
use App\Models\User\Journal\BlogCategoryContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Traits\Tenant\Frontend\PageHeadings;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;

class BlogController extends Controller
{

    use TenantFrontendLanguage, PageHeadings;
    public function index($username, Request $request)
    {
        $tenantId = getUser()->id;
        $misc = new MiscellaneousController();

        $language = $this->currentLang($tenantId);
        $queryResult['pageHeading'] = $this->pageHeading($tenantId);

        $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_blog', 'meta_description_blog')->first();


        $queryResult['breadcrumb'] = $misc->getBreadcrumb($tenantId);

        $postTitle = $blogCategorySlug = null;

        if ($request->filled('title')) {
            $postTitle = $request['title'];
        }
        if ($request->filled('category')) {
            $blogCategorySlug = $request['category'];
        }

        $queryResult['posts'] = Blog::where('user_blogs.user_id', $tenantId)
            ->join('user_blog_informations', 'user_blogs.id', '=', 'user_blog_informations.blog_id')
            ->join('user_blog_categories', 'user_blog_categories.id', '=', 'user_blogs.category_id')
            ->join('user_blog_category_contents', 'user_blog_category_contents.category_id', '=', 'user_blog_categories.id')
            ->where('user_blog_informations.language_id', '=', $language->id)
            ->when($postTitle, function (Builder $query, $postTitle) {
                return $query->where('user_blog_informations.title', 'like', '%' . $postTitle . '%');
            })
            ->when($blogCategorySlug, function ($query, $blogCategorySlug) {
                return $query->where('user_blog_category_contents.slug', '=', $blogCategorySlug);
            })
            ->where('user_blog_category_contents.language_id', '=', $language->id)
            ->select(
                'user_blogs.image',
                'user_blogs.category_id',
                'user_blog_informations.title',
                'user_blog_informations.content',
                'user_blog_informations.slug',
                'user_blog_informations.author',
                'user_blogs.created_at',
                'user_blog_category_contents.name as category_name',
                'user_blog_category_contents.slug as category_slug'
            )
            ->orderBy('user_blogs.serial_number', 'asc')
            ->paginate(15);

        $queryResult['recent_blogs'] =  Blog::recentBlogs($tenantId, $language->id, 3);

        $categories = BlogCategory::getCategories($tenantId, $language->id);
        $categories->map(function ($category) {
            $category['postCount'] = $category->blogs()->count();
        });
        $queryResult['categories'] = $categories;
        $queryResult['totalPost'] = $language->postInformation()->count();

        return view('tenant_frontend.blog.posts', $queryResult);
    }

    public function show($username, $slug)
    {
        $tenantId = getUser()->id;
        $misc = new MiscellaneousController();

        $language = $this->currentLang($tenantId);

        $queryResult['breadcrumb'] = $misc->getBreadcrumb($tenantId);

        $details = Blog::where('user_blogs.user_id', $tenantId)
            ->join('user_blog_informations', 'user_blogs.id', '=', 'user_blog_informations.blog_id')
            ->join('user_blog_categories', 'user_blog_categories.id', '=', 'user_blogs.category_id')
            ->join('user_blog_category_contents', 'user_blog_category_contents.category_id', '=', 'user_blog_categories.id')
            ->where('user_blog_informations.language_id', '=', $language->id)
            ->where('user_blog_category_contents.language_id', '=', $language->id)
            ->where('user_blog_informations.slug', '=', $slug)
            ->select(
                'user_blogs.id',
                'user_blogs.image',
                'user_blogs.category_id',
                'user_blogs.created_at',
                'user_blog_informations.title',
                'user_blog_informations.slug',
                'user_blog_informations.content',
                'user_blog_informations.meta_keywords',
                'user_blog_informations.author',
                'user_blog_informations.meta_description',
                'user_blog_category_contents.name as categoryName',
                'user_blog_category_contents.slug as categorySlug',
            )
            ->firstOrFail();

        $queryResult['details'] = $details;
        $queryResult['disqusInfo'] = BasicSetting::where('user_id', $tenantId)->select('disqus_status', 'disqus_short_name')->first();

        $categories = BlogCategory::getCategories($tenantId, $language->id);
        $categories->map(function ($category) {
            $category['postCount'] = $category->blogs()->count();
        });
        $queryResult['categories'] = $categories;

        $queryResult['totalPost'] = $language->postInformation()->count();
        $queryResult['recent_blogs'] =  Blog::whereNot('user_blogs.id', $details->id)->recentBlogs($tenantId, $language->id);

        return view('tenant_frontend.blog.post-details', $queryResult);
    }
}
