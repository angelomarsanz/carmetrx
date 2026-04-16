<?php

namespace App\Http\Controllers\User\HomePage;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\User\BasicSetting;
use App\Models\User\HomePage\HeroSlider;
use App\Models\User\HomePage\HeroStatic;
use App\Models\User\HomePage\HomePage;
use App\Models\User\Language;
use App\Rules\ImageMimeTypeRule;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class HeroController extends Controller
{
    use TenantFrontendLanguage;
    public function index(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;

        $language = $this->selectLang($tenantId, $request->language);

        $themeVersion = BasicSetting::where('user_id', $tenantId)->pluck('theme_version')->first();

        if ($themeVersion == 2) {
            $information['sliders'] = HeroSlider::where([['user_id', $tenantId], ['language_id', $language->id]])->orderByDesc('id')->get();

            return view('user.home-page.hero-section.slider.index', $information);
        } else {
            $information['heroImg'] = HomePage::where('user_id', $tenantId)->pluck('hero_static_img')->first();
            $information['heroInfo'] = HeroStatic::where([['user_id', $tenantId], ['language_id', $language->id]])->first();

            return view('user.home-page.hero-section.static.index', $information);
        }
    }

    public function storeSlider(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;

        $language = $this->selectLang($tenantId, $request->language);

        $rules = [
            'image' => [
                'required',
                new ImageMimeTypeRule()
            ],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        // store image in storage
        $imgName = UploadFile::store(Constant::WEBSITE_SLIDER_IMAGE . '/', $request->file('image'));

        HeroSlider::create([
            'image' => $imgName,
            'user_id' => Auth::guard('web')->user()->id,
            'language_id' => $language->id,
            'title' => $request->title,
            'text' => $request->text,
        ]);

        session()->flash('success', __('Added successfully!'));

        return 'success';
    }

    public function updateSlider(Request $request)
    {
        $rule = [
            'image' => $request->hasFile('image') ? new ImageMimeTypeRule() : ''
        ];

        $validator = Validator::make($request->all(), $rule);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $slider = HeroSlider::query()->find($request['id']);

        if ($request->hasFile('image')) {
            $newImage = $request->file('image');
            $oldImage = $slider->image;
            $imgName = UploadFile::update(Constant::WEBSITE_SLIDER_IMAGE . '/', $newImage, $oldImage);
        }

        $slider->update($request->except('image') + [
            'image' => $request->hasFile('image') ? $imgName : $slider->image
        ]);

        session()->flash('success', __('Updated successfully!'));

        return 'success';
    }

    public function destroySlider($id)
    {
        $slider = HeroSlider::query()->find($id);

        @unlink(public_path(Constant::WEBSITE_SLIDER_IMAGE . '/' . $slider->image));

        $slider->delete();

        return redirect()->back()->with('success', __('Deleted successfully!'));
    }


    public function updateImg(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;

        $img = HomePage::where('user_id', $userId)->first('hero_static_img')->hero_static_img ?? '';

        $rules = [];

        if (empty($img)) {
            $rules['image'] = 'required';
        }
        if ($request->hasFile('image')) {
            $rules['image'] = new ImageMimeTypeRule();
        }

        $request->validate($rules);

        if ($request->hasFile('image')) {
            $newImage = $request->file('image');
            $oldImage = $img;

            $imgName = UploadFile::update(Constant::WEBSITE_SLIDER_IMAGE . '/', $newImage, $oldImage);

            HomePage::updateOrCreate(
                [
                    'user_id' => $userId,

                ],
                ['hero_static_img' => $imgName]
            );

            session()->flash('success', __('Updated successfully!'));
        }

        return redirect()->back();
    }

    public function updateHeroInfo(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;

        if ($request->has('language')) {

            $language = $this->selectLang($tenantId, $request->language);
        } else {
            $language = $this->defaultLang($tenantId);
        }


        HeroStatic::updateOrCreate(
            [
                'language_id' => $language->id,
                'user_id' => $tenantId,
            ],
            [
                'title' => $request->title,
                'text' => $request->text
            ]
        );

        session()->flash('success', __('Updated successfully!'));

        return redirect()->back();
    }
}
