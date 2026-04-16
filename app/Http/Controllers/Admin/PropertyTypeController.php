<?php

namespace App\Http\Controllers\Admin;

use App\Models\BasicSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class PropertyTypeController extends Controller
{
    public function index()
    {
        return view('admin.property-management.property-type');
    }

    public function update(Request $request)
    {
        $bss = BasicSetting::all();
        foreach ($bss as $key => $bs) {
            $bs->is_rent_active = $request->is_rent_active;
            $bs->is_sale_active = $request->is_sale_active;
            $bs->save();
        }

        Session::flash('success', __('Updated successfully!'));
        return back();
    }


    public function settings()
    {
        $content = BasicSetting::select('property_country_status', 'property_state_status')->first();
        return view('admin.property-management.settings', compact('content'));
    }
    //update_setting
    public function update_settings(Request $request)
    {
        $request->validate([
            'property_country_status' => 'required',
            'property_state_status' => 'required',
        ]);
        $status = BasicSetting::first();
        $status->property_country_status = $request->property_country_status;
        $status->property_state_status = $request->property_state_status;
        $status->save();
        Session::flash('success', __('Updated successfully!'));
        return back();
    }
}
