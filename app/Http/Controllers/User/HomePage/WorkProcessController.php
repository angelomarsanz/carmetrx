<?php

namespace App\Http\Controllers\User\HomePage;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\User\HomePage\HomePage;
use App\Models\User\HomePage\SectionTitle;
use App\Models\User\HomePage\WorkProcess;
use App\Rules\ImageMimeTypeRule;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class WorkProcessController extends Controller
{
    use TenantFrontendLanguage;

    public function index(Request $request)
    {
        $userId =  Auth::guard('web')->user()->id;
        $language = $this->selectLang($userId, $request->language);
        $information['bgImg'] = HomePage::where('user_id', $userId)->pluck('work_process_bg_img')->first();
        $information['workProcessSecInfo'] = $language->workStepsSecInfo()->first();
        $information['workProcess'] = WorkProcess::where([['user_id', $userId], ['language_id', $language->id]])->orderBy('serial_number', 'asc')->get();

        return view('user.home-page.work-process-section.index', $information);
    }
    public function updateSectionInfo(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;
        $language = $this->selectLang($userId, $request->language);


        SectionTitle::updateOrCreate(
            [
                'user_id' => $userId,
                'language_id' => $language->id,
            ],
            [
                'work_process_title' => $request->work_process_title,
                'work_process_subtitle' => $request->work_process_subtitle,
            ]
        );

        Session::flash('success', __('Updated successfully!'));
        return redirect()->back();
    }

    public function updateBgImg(Request $request)
    {
        $userId = Auth::guard('web')->user()->id;

        $bgImg = HomePage::where('user_id', $userId)->pluck('work_process_bg_img')->first();

        $rules = [];

        if (empty($bgImg)) {
            $rules['work_process_bg_img'] = 'required';
        }
        if ($request->hasFile('work_process_bg_img')) {
            $rules['work_process_bg_img'] = new ImageMimeTypeRule();
        }

        $message = [
            'work_process_bg_img.required' => __('The background image field is required')
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        if ($request->hasFile('work_process_bg_img')) {
            $newImage = $request->file('work_process_bg_img');
            $oldImage = $bgImg;

            $imgName = UploadFile::update(Constant::WEBSITE_WORK_PROCESS_IMAGE . '/', $newImage, $oldImage);

            HomePage::updateOrCreate(
                [
                    'user_id' => Auth::guard('web')->user()->id,
                ],
                ['work_process_bg_img' => $imgName]
            );

            session()->flash('success', __('Updated successfully!'));
        }

        return redirect()->back();
    }

    public function store(Request $request)
    {
        $rules = [
            'icon' => 'required',
            'color' => 'required',
            'title' => 'required',
            'text' => 'required',
            'serial_number' => 'required|numeric'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['errors' => $validator->getMessageBag()], 400);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()
            ], 400);
        }

        $userId = Auth::guard('web')->user()->id;
        $language = $this->selectLang($userId, $request->language);

        $workProcess =  WorkProcess::create([
            'language_id' => $language->id,
            'user_id' => $userId,
            'icon' => $request->icon,
            'color' => $request->color,
            'title' => $request->title,
            'text' => $request->text,
            'serial_number' => $request->serial_number
        ]);

        if ($workProcess) {
            session()->flash('success', __('Added successfully!'));
        } else {
            session()->flash('warning', __('Something went wrong!'));
        }

        return Response::json(['success'], 200);
    }

    public function update(Request $request)
    {


        $rules = [
            'icon' => 'required',
            'color' => 'required',
            'title' => 'required',
            'text' => 'required',
            'serial_number' => 'required|numeric'
        ];


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['errors' => $validator->getMessageBag()], 400);
        }


        $workProcess = WorkProcess::findOrFail($request->id);
        $workProcess->update([
            'user_id' => Auth::guard('web')->user()->id,
            'icon' => $request->icon,
            'color' => $request->color,
            'title' => $request->title,
            'text' => $request->text,
            'serial_number' => $request->serial_number
        ]);

        session()->flash('success', __('Updated successfully!'));

        return 'success';
    }

    public function destroy($id)
    {
        $workProcess = WorkProcess::find($id);

        $workProcess->delete();

        return redirect()->back()->with('success', __('Deleted successfully!'));
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request['ids'];

        foreach ($ids as $id) {
            $workProcess = WorkProcess::find($id);

            $workProcess->delete();
        }

        session()->flash('success', __('Deleted successfully!'));

        return Response::json(['success'], 200);
    }
}
