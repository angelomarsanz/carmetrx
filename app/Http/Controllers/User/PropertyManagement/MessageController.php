<?php

namespace App\Http\Controllers\User\PropertyManagement;

use App\Http\Controllers\Controller;
use App\Models\User\Property\PropertyContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Support\Facades\Session;

class MessageController extends Controller
{
    use TenantFrontendLanguage;
    public function index(Request $request)
    {
        $tanant = Auth::guard('web')->user();
        if ($request->has('language')) {
            $language = $this->selectLang($tanant->id, $request->language);
        } else {
            $language = $this->defaultLang($tanant->id);
        }

        $title = null;

        if (request()->filled('title')) {
            $title = $request->title;
        }
        $messages = PropertyContact::where('user_property_contacts.user_id', $tanant->id)
            ->leftJoin('user_properties', 'user_property_contacts.property_id', 'user_properties.id')
            ->leftJoin('user_property_contents', 'user_properties.id', 'user_property_contents.property_id')
            ->where('user_property_contents.language_id', $language->id)
            ->when($title, function ($query) use ($title) {
                return $query->where('user_property_contents.title', 'LIKE', '%' . $title . '%');
            })
            ->leftJoin('user_agents', 'user_property_contacts.agent_id', '=', 'user_agents.id')
            ->select('user_property_contacts.*', 'user_property_contents.title', 'user_property_contents.slug', 'user_agents.username')
            ->latest()->get();

        return view('user.property-management.message', compact('messages'));
    }


    public function destroy(Request $request)
    {
        $message = PropertyContact::where('user_id', Auth::guard('web')->user()->id)->find($request->message_id);
        if ($message) {
            $message->delete();
            Session::flash('success', __('Deleted successfully!'));
        } else {
            Session::flash('warning', __('Something went wrong!'));
        }
        return redirect()->back();
    }
}
