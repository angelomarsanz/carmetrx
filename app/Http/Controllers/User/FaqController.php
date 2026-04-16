<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\FAQ;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    use TenantFrontendLanguage;
    public function index(Request $request)
    {
        $tenantId =  Auth::guard('web')->user()->id;
        $language = $this->selectLang($tenantId, $request->language);
        $information['faqs'] = $language->faqs()->orderBy('serial_number', 'asc')->get();

        return view('user.faq.index', $information);
    }

    public function store(Request $request)
    {
        $rules = [
            'question' => 'required',
            'answer' => 'required',
            'serial_number' => 'required'
        ];


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }

        $tenantId =  Auth::guard('web')->user()->id;
        $language = $this->selectLang($tenantId, $request->language);

        FAQ::create([
            'language_id' => $language->id,
            'user_id' => $tenantId,
            'question' => $request->question,
            'answer' => $request->answer,
            'serial_number' => $request->serial_number
        ]);

        Session::flash('success', __('Added successfully!'));
        return "success";
    }

    public function update(Request $request)
    {
        $rules = [
            'question' => 'required',
            'answer' => 'required',
            'serial_number' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }
        FAQ::where('user_id', Auth::guard('web')->user()->id)->find($request->id)->update(
            [
                'question' => $request->question,
                'answer' => $request->answer,
                'serial_number' => $request->serial_number
            ]
        );
        Session::flash('success', __('Updated successfully!'));
        return "success";
    }

    public function destroy($id)
    {
        FAQ::find($id)->delete();
        return redirect()->back()->with('success', __('Deleted successfully!'));
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            FAQ::find($id)->delete();
        }
        Session::flash('success', __('Deleted successfully!'));
        return "success";
    }
}
