<?php

namespace App\Http\Controllers\User\HomePage;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Http\Requests\Testimonial\StoreRequest;
use App\Http\Requests\Testimonial\UpdateRequest;
use App\Models\User\HomePage\HomePage;
use App\Models\User\HomePage\Testimonial;
use App\Models\User\HomePage\TestimonialSection;
use App\Rules\ImageMimeTypeRule;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
    use TenantFrontendLanguage;

    public function index(Request $request)
    {
        $tenantId =  Auth::guard('web')->user()->id;
        $language = $this->selectLang($tenantId, $request->language);
        $information['bgImg'] = HomePage::where('user_id', $tenantId)->pluck('testimonial_bg_img')->first();
        $information['sectionInfo'] =  $language->testimonialSecInfo()->first();
        $information['testimonials'] = Testimonial::where([['user_id', $tenantId], ['language_id', $language->id]])->orderByDesc('id')->get();

        return view('user.home-page.testimonial-section.index', $information);
    }
    public function updateBgImg(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;
        $bgImg = HomePage::where('user_id', $userId)->pluck('testimonial_bg_img')->first();

        $rules = [];

        if (empty($bgImg)) {
            $rules['testimonial_bg_img'] = 'required';
        }
        if ($request->hasFile('testimonial_bg_img')) {
            $rules['testimonial_bg_img'] = new ImageMimeTypeRule();
        }

        $message = [
            'testimonial_bg_img.required' => __('The background image field is required')
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        if ($request->hasFile('testimonial_bg_img')) {
            $newImage = $request->file('testimonial_bg_img');
            $oldImage = $bgImg;

            $imgName = UploadFile::update(Constant::WEBSITE_TESTIMONIAL_SECTION_IMAGE . '/', $newImage, $oldImage);

            HomePage::updateOrCreate(
                [
                    'user_id' => $userId,
                ],
                ['testimonial_bg_img' => $imgName]
            );

            session()->flash('success', __('Updated successfully!'));
        }

        return redirect()->back();
    }

    public function updateSectionInfo(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;
        $language = $this->selectLang($userId, $request->language);


        $testimonialSection =    TestimonialSection::updateOrCreate(
            [
                'user_id' => $userId,
                'language_id' => $language->id,
            ],
            [
                'subtitle' => $request->subtitle,
                'title' => $request->title,
                'content' => $request->content,
            ]
        );

        if ($testimonialSection) {
            Session::flash('success', __('Updated successfully!'));
        } else {
            Session::flash('warning', __('Something went wrong!'));
        }


        return redirect()->back();
    }
    public function storeTestimonial(StoreRequest $request)
    {
        $userId = Auth::guard('web')->user()->id;
        $language = $this->selectLang($userId, $request->language);

        // store image in storage
        $imgName = UploadFile::store(Constant::WEBSITE_TESTIMONIAL_IMAGE . '/', $request->file('image'));

        Testimonial::create([
            'user_id' =>  $userId,
            'language_id' => $language->id,
            'image' => $imgName,
            'name' => $request->name,
            'occupation' => $request->occupation,
            'comment' => $request->comment,
            'rating' => $request->rating,
        ]);

        session()->flash('success', __('Added successfully!'));

        return 'success';
    }

    public function updateTestimonial(UpdateRequest $request)
    {
        $testimonial = Testimonial::find($request->id);

        if ($request->hasFile('image')) {
            $newImage = $request->file('image');
            $oldImage = $testimonial->image;
            $imgName = UploadFile::update(Constant::WEBSITE_TESTIMONIAL_IMAGE . '/', $newImage, $oldImage);
        }

        $testimonial->update([
            'image' => $request->hasFile('image') ? $imgName : $testimonial->image,
            'name' => $request->name,
            'occupation' => $request->occupation,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        session()->flash('success', __('Updated successfully!'));

        return 'success';
    }

    public function destroyTestimonial($id)
    {
        $testimonial = Testimonial::query()->find($id);

        @unlink(public_path(Constant::WEBSITE_TESTIMONIAL_IMAGE . '/' . $testimonial->image));

        $testimonial->delete();

        return redirect()->back()->with('success', __('Deleted successfully!'));
    }

    public function bulkDestroyTestimonial(Request $request)
    {
        $ids = $request['ids'];

        foreach ($ids as $id) {
            $testimonial = Testimonial::query()->find($id);

            @unlink(public_path(Constant::WEBSITE_TESTIMONIAL_IMAGE . '/' . $testimonial->image));

            $testimonial->delete();
        }

        session()->flash('success', __('Deleted successfully!'));

        return response()->json(['status' => 'success'], 200);
    }
}
