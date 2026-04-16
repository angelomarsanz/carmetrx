<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User\Menu;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuBuilderController extends Controller
{
    use TenantFrontendLanguage;
    public function index(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;

        if ($request->has('language')) {
            $language = $this->selectLang($tenantId, $request->language);
        } else {

            $language = $this->defaultLang($tenantId);
        }
        // get previous menus
        $menu = Menu::where([['user_id', $tenantId], ['language_id', $language->id]])->first();
        $data['lang_id'] = $language->id;
        $data['prevMenu'] = '';
        if (!empty($menu)) {
            $data['prevMenu'] = $menu->menus;
        }
        $data['apages'] = DB::table('user_pages')
            ->join('user_page_contents', 'user_pages.id', '=', 'user_page_contents.page_id')
            ->where('user_page_contents.language_id', '=', $language->id)
            ->where('user_page_contents.user_id', '=', $tenantId)
            ->orderByDesc('user_pages.id')
            ->get(); 
        return view('user.menu_builder.index', $data);
    }

    public function update(Request $request)
    {
        $tenantId = Auth::guard('web')->user()->id;
        $menu =  Menu::updateOrCreate([
            'user_id' => $tenantId,
            'language_id' => $request->language_id,
        ], [
            'menus' => json_decode($request['str'])
        ]);

        if ($menu) {
            $message = ['status' => 'success', 'message' => __('Updated successfully!')];
        } else {
            $message = ['status' => 'warning', 'message' => __('Something went wrong!')];
        }
        return response()->json($message);
    }
}
