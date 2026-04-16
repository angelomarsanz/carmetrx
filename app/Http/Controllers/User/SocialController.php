<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\Social;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SocialController extends Controller
{


    public function index()
    {
        $data['socials'] = Social::where('user_id', Auth::guard('web')->user()->id)
            ->orderBy('serial_number', 'asc')
            ->get();
        return view('user.settings.social-media.index', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'icon' => 'required',
            'url' => 'required',
            'serial_number' => 'required|integer',
        ]);

        Social::create($request->except('user_id') + [
            'user_id' => Auth::guard('web')->user()->id,

        ]);

        Session::flash('success', __('Added successfully!'));
        return "success";
    }

    public function edit($id)
    {
        $data['social'] = Social::where([['user_id', Auth::guard('web')->user()->id]])->where('id', $id)->firstOrFail();
        return view('user.settings.social.edit', $data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'icon' => 'required',
            'url' => 'required',
            'serial_number' => 'required|integer',
        ]);
        Social::where([['user_id', Auth::guard('web')->user()->id]])
            ->where('id', $request->social_id)
            ->update(request()->except(['_token', 'social_id']));
        Session::flash('success', __('Updated successfully!'));
        return "success";
    }

    public function delete(Request $request)
    {

        Social::where([['user_id', Auth::guard('web')->user()->id]])->where('id', $request->social_id)->delete();
        Session::flash('success', __('Deleted successfully!'));
        return back();
    }
}
