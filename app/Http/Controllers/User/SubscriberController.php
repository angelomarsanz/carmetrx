<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\BasicMailer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Models\User\Subscriber;
use App\Models\BasicSetting;
use App\Models\BasicExtended;
use App\Mail\ContactMail;
use App\Models\Customer;
use App\Models\User\BasicSetting as UserBasicSetting;
use Mail;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->term;
        $data['subscs'] = Subscriber::where('user_id', Auth::guard('web')->user()->id)
            ->when($term, function ($query, $term) {
                return $query->where('email', 'LIKE', '%' . $term . '%');
            })->orderBy('id', 'DESC')->paginate(10);
        return view('user.subscribers.index', $data);
    }

    public function store(Request $request, $domain)
    {
        $user = getUser();
        $request->validate([
            'email' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    $subscriber = Subscriber::where([
                        ['email', $value],
                        ['user_id', $user->id]
                    ])->get();
                    if ($subscriber->count() > 0) {
                        Session::flash('error', __('This email is already subscribed'));
                        $fail(':attribute already subscribed for this user');
                    }
                },
            ],
        ]);
        $request['user_id'] = $user->id;
        Subscriber::create($request->all());
        return Response::json([
            'success' => __('You have successfully subscribed to our newsletter')
        ]);
    }

    public function mailsubscriber()
    {

        return view('user.subscribers.mail');
    }

    public function subscsendmail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string',
            'message' => 'required'
        ]); 
        $sub = $request->subject;
        $msg = $request->message;
        $data['subject'] = $request->subject;
        $data['body'] = $request->message;

        $subscribers = Subscriber::where('user_id', Auth::guard('web')->user()->id)->get();
        if (is_null($subscribers)) {
            Session::flash('warning', __('No Subscriber Found!'));
            return back();
        }
        foreach ($subscribers as $sub) {
            $data['recipient'] = $sub->email;
            BasicMailer::sendMail(Auth::guard('web')->user()->id, session()->get('user_dashboard_website'), $data);
        }

      

        Session::flash('success', __('Mail sent successfully!'));
        return back();
    }


    public function delete(Request $request)
    {
        Subscriber::findOrFail($request->subscriber_id)->delete();
        Session::flash('success', __('Deleted successfully!'));
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        foreach ($ids as $id) {
            Subscriber::findOrFail($id)->delete();
        }
        Session::flash('success', __('Deleted successfully!'));
        return "success";
    }
}
