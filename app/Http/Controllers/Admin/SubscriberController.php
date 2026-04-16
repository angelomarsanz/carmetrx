<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\Subscriber;
use App\Models\BasicSetting;
use App\Models\BasicExtended;
use Illuminate\Support\Facades\Session;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->term;
        $data['subscs'] = Subscriber::when($term, function ($query, $term) {
            return $query->where('email', 'LIKE', '%' . $term . '%');
        })->orderBy('id', 'DESC')->paginate(10);
        return view('admin.subscribers.index', $data);
    }

    public function mailsubscriber()
    {
        return view('admin.subscribers.mail');
    }

    public function subscsendmail(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'message' => 'required'
        ]);

        $sub = $request->subject;
        $msg = $request->message;

        $subscs = Subscriber::all();
        $settings = BasicSetting::first();
        $from = $settings->contact_mail;

        $be = BasicExtended::first();


        $mail = new PHPMailer(true);

        if ($subscs->isEmpty()) {
            Session::flash('warning', __('No subscribers found to send the email') . '!');
            return back();
        }

        if ($be->is_smtp == 1) {
            try {
                //Server settings
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = $be->smtp_host;                    // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = $be->smtp_username;                     // SMTP username
                $mail->Password   = $be->smtp_password;                               // SMTP password
                $mail->SMTPSecure = $be->encryption;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port       = $be->smtp_port;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                //Recipients
                $mail->setFrom($be->from_mail, $be->from_name);

                foreach ($subscs as $key => $subsc) {
                    $mail->addBCC($subsc->email);     // Add a recipient
                }
            } catch (Exception $e) {
            }
        } else {
            try {

                //Recipients
                $mail->setFrom($be->from_mail, $be->from_name);
                foreach ($subscs as $key => $subsc) {
                    $mail->addBCC($subsc->email);     // Add a recipient
                }
            } catch (Exception $e) {
            }
        }

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $sub;
        $mail->Body    = $msg;

        $mail->send();

        Session::flash('success', __('Mail sent successfully!'));
        return back();
    }


    public function delete(Request $request)
    {

        $subscriber = Subscriber::findOrFail($request->subscriber_id);
        $subscriber->delete();

        Session::flash('success', __('Deleted successfully!'));
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $subscriber = Subscriber::findOrFail($id);
            $subscriber->delete();
        }

        Session::flash('success', __('Deleted successfully!'));
        return "success";
    }
}
