<?php

namespace App\Http\Controllers\User\HomePage;

use App\Http\Controllers\Controller;
use App\Http\Requests\SectionStatusRequest;
use App\Models\User\AdditionalSection;
use App\Models\User\BasicSetting;
use App\Models\User\HomePage\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SectionController extends Controller
{


  public function index()
  {
    $tenantId = Auth::guard('web')->user()->id;
    $sectionInfo = Section::where('user_id', $tenantId)->first();
    if (empty($sectionInfo)) {
      Section::create([
        'user_id' => Auth::guard('web')->user()->id,
      ]);
      $sectionInfo = Section::where('user_id', $tenantId)->first();
    }
    $themeVersion = BasicSetting::where('user_id', $tenantId)->pluck('theme_version')->first();
    $customSecStatus = BasicSetting::where('user_id', $tenantId)->select('additional_section_status')->first();
    $customSectonStatus = json_decode($customSecStatus->additional_section_status, true);
    $customSectons = AdditionalSection::where('user_id', $tenantId)->where('page_type', 'home')->get();

    return view('user.home-page.section-customization', compact('sectionInfo', 'themeVersion', 'customSectons', 'customSectonStatus'));
  }

  public function update(SectionStatusRequest $request)
  {
    $userId = Auth::guard('web')->user()->id;
    $sectionInfo = Section::where('user_id', $userId)->first();
    $sectionInfo->update($request->except('_token'));

    $bs = BasicSetting::where('user_id', $userId)->first();
    $bs->additional_section_status = json_encode($request->additional_section_status, true);
    $bs->save();

    session()->flash('success', __('Updated successfully!'));
    return redirect()->back();
  }
}
