<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Social;
use Illuminate\Support\Facades\Session;

class SocialController extends Controller
{
  public function index()
  {
    $data['socials'] = Social::orderBy('serial_number', 'asc')->get();
    return view('admin.basic.social.index', $data);
  }

  public function store(Request $request)
  {
    $request->validate([
      'icon' => 'required',
      'url' => 'required',
      'serial_number' => 'required|integer',
    ]);

    $social = new Social;
    $social->icon = $request->icon;
    $social->url = $request->url;
    $social->serial_number = $request->serial_number;
    $social->save();

    Session::flash('success', __('Added successfully!'));
    return back();
  }

  public function edit($id)
  {
    $data['social'] = Social::findOrFail($id);
    return view('admin.basic.social.edit', $data);
  }

  public function update(Request $request)
  {
    $request->validate([
      'icon' => 'required',
      'url' => 'required',
      'serial_number' => 'required|integer',
    ]);

    $social = Social::findOrFail($request->socialid);
    $social->icon = $request->icon;
    $social->url = $request->url;
    $social->serial_number = $request->serial_number;
    $social->save();

    Session::flash('success', __('Updated successfully!'));
    return back();
  }

  public function delete(Request $request)
  {

    $social = Social::findOrFail($request->socialid);
    $social->delete();

    Session::flash('success', __('Deleted successfully!'));
    return back();
  }
}
