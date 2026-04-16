<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Process;
use App\Traits\AdminLanguage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProcessController extends Controller
{
    use AdminLanguage;
    public function index(Request $request)
    {
        $lang = $this->selectLang($request->language);
        $lang_id = $lang->id;
        $data['processes'] = Process::where('language_id', $lang_id)->orderBy('serial_number', 'asc')->get();
        $data['lang_id'] = $lang_id;
        return view('admin.home.process.index', $data);
    }

    public function edit($id)
    {
        $data['process'] = Process::findOrFail($id);

        return view('admin.home.process.edit', $data);
    }

    public function store(Request $request)
    {

        $img = $request->file('image');
        $allowedExts = array('jpg', 'png', 'jpeg');


        $rules = [
            'language' => 'required',
            'icon' => 'required',
            'title' => 'required|max:100',
            'text' => 'required|max:255',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        Process::storeProcess($request);

        Session::flash('success', __('Added successfully!'));
        return "success";
    }

    public function update(Request $request)
    {
        $rules = [
            'icon' => 'required',
            'title' => 'required|max:100',
            'text' => 'required|max:255',
            'serial_number' => 'required|integer',
        ];

        $request->validate($rules);
        $process = Process::findOrFail($request->process_id);
        if ($process) {
            $process->updateProcess($request);
            Session::flash('success', __('Updated successfully!'));
        }


        return back();
    }

    public function delete(Request $request)
    {

        $process = Process::findOrFail($request->process_id);

        $process->delete();

        Session::flash('success', __('Deleted successfully!'));
        return back();
    }

    public function removeImage(Request $request)
    {
        $type = $request->type;
        $featId = $request->process_id;

        $process = Process::findOrFail($featId);

        if ($type == "process") {
            // @unlink(public_path("assets/front/img/process/" . $process->image));
            $process->image = NULL;
            $process->save();
        }

        $request->session()->flash('success', __('Image removed successfully!'));
        return "success";
    }
}
