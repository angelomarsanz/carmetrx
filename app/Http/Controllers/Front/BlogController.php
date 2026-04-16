<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User\BasicSetting;
use App\Models\User\Journal\Blog;
use App\Models\User\Journal\BlogCategory;
use App\Models\User\Journal\BlogInformation;
use App\Models\User\SEO;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function getBlogCategories($languageId, $userId)
    {
        $categories = BlogCategory::query()->where('user_id', $userId)->where('language_id', $languageId)->where('status', 1)->orderBy('serial_number', 'asc')->get();
        $categories->map(function ($category) {
            $category['blogCount'] = BlogInformation::query()->where('blog_category_id', '=', $category->id)->count();
        });
        return $categories;
    }

    public function blogs(Request $request, $domain)
    {
        $user = getUser();

        $language = $this->getUserCurrentLanguage($user->id);

        $queryResult['seoInfo'] = SEO::query()->where('user_id', $user->id)->select('blogs_meta_keywords', 'blogs_meta_description')->first();

        $queryResult['pageHeading'] = $this->getUserPageHeading($language, $user->id);

        $queryResult['bgImg'] = $this->getUserBreadcrumb($user->id);

        $blogTitle = $blogCategory = null;

        if ($request->filled('title')) {
            $blogTitle = $request['title'];
        }
        if ($request->filled('category')) {
          $blogCategory = BlogCategory::where('slug', $request['category'])->where('user_id', $user->id)->first()->id;
        }

        $queryResult['blogs'] = Blog::query()->join('user_blog_informations', 'user_blogs.id', '=', 'user_blog_informations.blog_id')
            ->join('user_blog_categories', 'user_blog_categories.id', '=', 'user_blog_informations.blog_category_id')
            ->where('user_blog_informations.language_id', '=', $language->id)
            ->where('user_blog_informations.user_id', '=', $user->id)
            ->when($blogTitle, function ($query, $blogTitle) {
                return $query->where('user_blog_informations.title', 'like', '%' . $blogTitle . '%');
            })
            ->when($blogCategory, function ($query, $blogCategory) {
              return $query->where('user_blog_informations.blog_category_id', '=', $blogCategory);
            })
            ->select('user_blogs.image', 'user_blogs.created_at', 'user_blog_informations.title', 'user_blog_informations.slug', 'user_blog_informations.content', 'user_blog_categories.name as categoryName', 'user_blog_categories.slug as categorySlug')
            ->orderBy('user_blogs.serial_number', 'asc')
            ->paginate(6);


        $queryResult['categories'] = $this->getBlogCategories($language->id, $user->id);

        $queryResult['allBlogs'] = BlogInformation::query()->where('user_id', $user->id)->where('language_id', $language->id)->count();

        return view('user-front.common.journal.blogs', $queryResult);
    }

    public function details($domain,$slug)
    {
        $user = getUser();
        $language = $this->getUserCurrentLanguage($user->id);
        $queryResult['pageHeading'] = $this->getUserPageHeading($language, $user->id);
        $queryResult['bgImg'] = $this->getUserBreadcrumb($user->id);

        $queryResult['details'] = Blog::query()->join('user_blog_informations', 'user_blogs.id', '=', 'user_blog_informations.blog_id')
            ->join('user_blog_categories', 'user_blog_categories.id', '=', 'user_blog_informations.blog_category_id')
            ->where('user_blog_informations.language_id', '=', $language->id)
            ->where('user_blog_informations.slug', '=', $slug)
            ->where('user_blog_informations.user_id', '=', $user->id)
            ->select('user_blogs.image', 'user_blogs.created_at', 'user_blog_informations.title', 'user_blog_informations.slug', 'user_blog_informations.content', 'user_blog_informations.meta_keywords', 'user_blog_informations.meta_description', 'user_blog_categories.name as categoryName')
            ->firstOrFail();

        $categoryId = BlogInformation::query()
            ->where('language_id', $language->id)
            ->where('slug', $slug)
            ->where('user_id', $user->id)
            ->pluck('blog_category_id')
            ->first();

        $queryResult['relatedBlogs'] = Blog::query()->join('user_blog_informations', 'user_blogs.id', '=', 'user_blog_informations.blog_id')
            ->where('user_blog_informations.language_id', '=', $language->id)
            ->where('user_blog_informations.blog_category_id', '=', $categoryId)
            ->where('user_blog_informations.slug', '<>', $slug)
            ->select('user_blogs.image', 'user_blogs.created_at', 'user_blog_informations.title', 'user_blog_informations.slug', 'user_blog_informations.content')
            ->orderBy('user_blogs.serial_number', 'ASC')
            ->limit(4)
            ->get();

        $queryResult['disqusInfo'] = BasicSetting::query()->where('user_id', $user->id)->select('disqus_status', 'disqus_short_name')->firstOrFail();

        $queryResult['categories'] = $this->getBlogCategories($language->id, $user->id);

        $queryResult['allBlogs'] = BlogInformation::query()->where('user_id', $user->id)->where('language_id', $language->id)->count();

        return view('user-front.common.journal.blog-details', $queryResult);
    }
}
