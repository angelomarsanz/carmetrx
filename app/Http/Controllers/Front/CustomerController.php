<?php

namespace App\Http\Controllers\Front;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Uploader;
use App\Http\Helpers\UploadFile;
use App\Http\Helpers\UserPermissionHelper;
use App\Http\Requests\UserProfileRequest;
use App\Models\Customer;
use App\Models\EmailTemplate;
use App\Models\User\Curriculum\Course;
use App\Models\User\Curriculum\CourseInformation;
use App\Models\User\Curriculum\Lesson;
use App\Models\User\Curriculum\LessonContent;
use App\Models\User\Curriculum\LessonQuiz;
use App\Models\User\Curriculum\Module;
use App\Models\User\Curriculum\QuizScore;
use App\Models\User\Language;
use App\Models\User;
use App\Models\User\BasicSetting;
use App\Models\User\BookmarkPost;
use App\Models\User\Curriculum\CourseEnrolment;
use App\Models\User\LessonComplete;
use App\Models\User\LessonContentComplete;
use App\Models\User\MailTemplate;
use App\Models\User\SEO;
use Config;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class CustomerController extends Controller
{

    public function login(Request $request, $domain)
    {
        $user = getUser();

        $language = $this->getUserCurrentLanguage($user->id);
        $queryResult['pageHeading'] = $this->getUserPageHeading($language, $user->id);
        $queryResult['bgImg'] = $this->getUserBreadcrumb($user->id);

        $queryResult['seoInfo'] = SEO::query()
            ->where('user_id', $user->id)
            ->select('login_meta_keywords', 'login_meta_description')
            ->first();

        // when user have to redirect to check out page after login.
        if ($request->input('redirectPath') == 'course_details') {
            $url = url()->previous();
        }

        // when user have to redirect to course details page after login.
        if (isset($url)) {
            $request->session()->put('redirectTo', $url);
        }
        return view('user-front.common.customer.auth.login', $queryResult);
    }

    public function loginSubmit(Request $request, $domain)
    {
        // at first, get the url from session which will be redirected after login
        if ($request->session()->has('redirectTo')) {
            $redirectURL = $request->session()->get('redirectTo');
        } else {
            $redirectURL = null;
        }

        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // get the email and password which has provided by the user
        $credentials = $request->only('email', 'password', 'user_id');

        // login attempt
        if (Auth::guard('customer')->attempt($credentials)) {
            $authUser = Auth::guard('customer')->user();

            // first, check whether the user's email address verified or not
            if ($authUser->email_verified_at == null) {
                $request->session()->flash('error', 'Please, verify your email address.');

                // logout auth user as condition not satisfied
                Auth::guard('customer')->logout();

                return redirect()->back();
            }

            // second, check whether the user's account is active or not
            if ($authUser->status == 0) {
                $request->session()->flash('error', 'Sorry, your account has been deactivated.');

                // logout auth user as condition not satisfied
                Auth::guard('customer')->logout();

                return redirect()->back();
            }

            // otherwise, redirect auth user to next url
            if ($redirectURL == null) {
                return redirect()->route('customer.dashboard', getParam());
            } else {
                // before, redirect to next url forget the session value
                $request->session()->forget('redirectTo');

                return redirect($redirectURL);
            }
        } else {
            $request->session()->flash('error', 'The provided credentials do not match our records!');

            return redirect()->back();
        }
    }

    public function forgetPassword($domain)
    {
        $user = getUser();
        $language = $this->getUserCurrentLanguage($user->id);
        $queryResult['seoInfo'] = SEO::query()
            ->where('user_id', $user->id)
            ->select('forget_password_meta_keywords', 'forget_password_meta_description')
            ->first();
        $queryResult['pageHeading'] = $this->getUserPageHeading($language, $user->id);
        $queryResult['bgImg'] = $this->getUserBreadcrumb($user->id);
        return view('user-front.common.customer.auth.forget-password', $queryResult);
    }
    public function sendMail(Request $request)
    {
        $user = getUser();

        $rules = [
            'email' => [
                'required',
                'email:rfc,dns',
                function ($attribute, $value, $fail) use ($request, $user) {
                    if (Customer::where('email', $request->email)->where('user_id', $user->id)->count() == 0) {
                        $fail('No record found for ' . $request->email);
                    }
                }
            ]
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $customer = Customer::where('email', $request->email)->where('user_id', $user->id)->first();

        // first, get the mail template information from db
        $mailTemplate = MailTemplate::where('mail_type', 'reset_password')->where('user_id', $user->id)->first();
        $mailSubject = $mailTemplate->mail_subject;
        $mailBody = $mailTemplate->mail_body;

        // second, send a password reset link to user via email
        $be = DB::table('basic_extendeds')
            ->select('is_smtp', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
            ->first();
        $userBs = BasicSetting::where('user_id', $user->id)->select('website_title', 'email', 'from_name')->first();

        $name = $customer->first_name . ' ' . $customer->last_name;

        $link = '<a href=' . route('customer.reset_password', getParam()) . '>Click Here</a>';

        $mailBody = str_replace('{customer_name}', $name, $mailBody);
        $mailBody = str_replace('{password_reset_link}', $link, $mailBody);
        $mailBody = str_replace('{website_title}', $userBs->website_title, $mailBody);

        // initialize a new mail
        $mail = new PHPMailer(true);

        // if smtp status == 1, then set some value for PHPMailer
        if ($be->is_smtp == 1) {
            $mail->isSMTP();
            $mail->Host       = $be->smtp_host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $be->smtp_username;
            $mail->Password   = $be->smtp_password;

            if ($be->encryption == 'TLS') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->Port = $be->smtp_port;
        }

        // finally, add other information and send the mail
        try {
            $mail->setFrom($be->from_mail, $userBs->from_name);
            $mail->addReplyTo($userBs->email, $userBs->from_name);
            $mail->addAddress($request->email);

            $mail->isHTML(true);
            $mail->Subject = $mailSubject;
            $mail->Body = $mailBody;

            $mail->send();

            $request->session()->flash('success', 'A mail has been sent to your email address.');
        } catch (Exception $e) {
            $request->session()->flash('error', 'Mail could not be sent!');
        }

        // store user email in session to use it later
        $request->session()->put('userEmail', $customer->email);

        return redirect()->back();
    }


    public function resetPassword($domain)
    {
        $user = getUser();
        return view('user-front.common.customer.auth.reset-password', ['bgImg' => $this->getUserBreadcrumb($user->id)]);
    }

    public function resetPasswordSubmit(Request $request, $domain)
    {
        $author = getUser();
        // get the user email from session
        $emailAddress = $request->session()->get('userEmail');

        $rules = [
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required'
        ];

        $messages = [
            'new_password.confirmed' => 'Password confirmation failed.',
            'new_password_confirmation.required' => 'The confirm new password field is required.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = Customer::where('email', $emailAddress)->where('user_id', $author->id)->first();

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        $request->session()->flash('success', 'Password updated successfully.');

        return redirect()->route('customer.login', getParam());
    }

    public function signup($domain)
    {
        $user = getUser();
        $language = $this->getUserCurrentLanguage($user->id);
        $queryResult['pageHeading'] = $this->getUserPageHeading($language, $user->id);
        $queryResult['bgImg'] = $this->getUserBreadcrumb($user->id);
        $queryResult['seoInfo'] = SEO::query()
            ->where('user_id', $user->id)
            ->select('sign_up_meta_keywords', 'sign_up_meta_description')
            ->first();
        return view('user-front.common.customer.auth.signup', $queryResult);
    }

    public function signupSubmit(Request $request, $domain)
    {
        $user = getUser();

        $rules = [
            'username' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($user) {
                    if (Customer::where('username', $value)->where('user_id', $user->id)->count() > 0) {
                        $fail('Username has already been taken');
                    }
                }
            ],
            'email' => ['required', 'email', 'max:255', function ($attribute, $value, $fail) use ($user) {
                if (Customer::where('email', $value)->where('user_id', $user->id)->count() > 0) {
                    $fail('Email has already been taken');
                }
            }],
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $customer = new Customer;
        $customer->username = $request->username;
        $customer->email = $request->email;
        $customer->user_id = $user->id;
        $customer->password = Hash::make($request->password);

        // first, generate a random string
        $randStr = Str::random(20);

        // second, generate a token
        $token = md5($randStr . $request->username . $request->email);

        $customer->verification_token = $token;
        $customer->save();

        // send a mail to user for verify his/her email address
        $this->sendVerificationMail($request, $token);

        return redirect()
            ->back()
            ->with('sendmail', 'We need to verify your email address. We have sent an email to  ' . $request->email . ' to verify your email address. Please click link in that email to continue.');
    }

    public function sendVerificationMail(Request $request, $token)
    {
        $user = getUser();
        $userId = $user->id;

        // first get the mail template information from db
        $mailTemplate = MailTemplate::where('mail_type', 'verify_email')->where('user_id', $userId)->first();
        $mailSubject = $mailTemplate->mail_subject;
        $mailBody = $mailTemplate->mail_body;

        // second get the website title & mail's smtp information from db
        $be = DB::table('basic_extendeds')
            ->select('is_smtp', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
            ->first();
        $userBs = DB::table('user_basic_settings')
            ->select('website_title', 'email', 'from_name')->where('user_id', $userId)
            ->first();

        $link = '<a href=' . route('customer.signup.verify', ['token' => $token, getParam()]) . '>Click Here</a>';

        // replace template's curly-brace string with actual data
        $mailBody = str_replace('{username}', $request->username, $mailBody);
        $mailBody = str_replace('{verification_link}', $link, $mailBody);
        $mailBody = str_replace('{website_title}', $userBs->website_title, $mailBody);


        // initialize a new mail
        $mail = new PHPMailer(true);

        // if smtp status == 1, then set some value for PHPMailer
        if ($be->is_smtp == 1) {
            $mail->isSMTP();
            $mail->Host       = $be->smtp_host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $be->smtp_username;
            $mail->Password   = $be->smtp_password;

            if ($be->encryption == 'TLS') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->Port = $be->smtp_port;
        }

        // finally, add other information and send the mail
        try {
            $mail->setFrom($be->from_mail, $userBs->from_name);
            $replyMail = $userBs->email ?? $user->email;
            $fromName = $userBs->from_name ?? $user->username;
            $mail->addReplyTo($replyMail, $fromName);
            $mail->addAddress($request->email);

            $mail->isHTML(true);
            $mail->Subject = $mailSubject;
            $mail->Body = $mailBody;

            $mail->send();

            $request->session()->flash('success', 'A verification mail has been sent to your email address.');
        } catch (Exception $e) {
            $request->session()->flash('error', 'Mail could not be sent!');
        }
    }

    public function signupVerify(Request $request, $domain, $token)
    {
        try {
            $user = Customer::where('verification_token', $token)->firstOrFail();
            // after verify user email, put "null" in the "verification token"
            $user->update([
                'email_verified_at' => date('Y-m-d H:i:s'),
                'status' => 1,
                'verification_token' => null
            ]);

            $request->session()->flash('success', 'Your email has verified.');

            // after email verification, authenticate this user
            Auth::guard('customer')->login($user);

            return redirect()->route('customer.dashboard', getParam());
        } catch (ModelNotFoundException $e) {
            $request->session()->flash('error', 'Could not verify your email!');
            return redirect()->route('customer.signup', getParam());
        }
    }

    public function redirectToDashboard($domain)
    {
        $author = getUser();

        $queryResult['bgImg'] = $this->getUserBreadcrumb($author->id);

        $user = Auth::guard('customer')->user();

        $queryResult['authUser'] = $user;

        return view('user-front.common.customer.dashboard', $queryResult);
    }

    public function editProfile($domain)
    {
        $user = getUser();
        $queryResult['bgImg'] = $this->getUserBreadcrumb($user->id);
        $queryResult['authUser'] = Auth::guard('customer')->user();
        return view('user-front.common.customer.edit-profile', $queryResult);
    }

    public function updateProfile(UserProfileRequest $request, $domain)
    {
        $user = getUser();
        $authUser = Auth::guard('customer')->user();
        $filename = $authUser->image;
        $directory = Constant::CUSTOMER_IMAGE . '/' . $user->id;
        if ($request->hasFile('image')) {
            $filename = UploadFile::update($directory, $request->file('image'), $authUser->image, $user->id);
        }
        $authUser->update($request->except('image', 'edit_profile_status') + [
            'image' => $filename,
            'edit_profile_status' => 1
        ]);
        $request->session()->flash('success', __('Updated successfully'));
        return redirect()->back();
    }











     

    public function updatePassword(Request $request, $domain)
    {
        $rules = [
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::guard('customer')->user()->password)) {
                        $fail('Your password was not updated, since the provided current password does not match.');
                    }
                }
            ],
            'new_password' => 'required|confirmed',
            'new_password_confirmation' => 'required'
        ];

        $messages = [
            'new_password.confirmed' => 'Password confirmation failed.',
            'new_password_confirmation.required' => 'The confirm new password field is required.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = Auth::guard('customer')->user();

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        $request->session()->flash('success', 'Password updated successfully.');
        return redirect()->back();
    }

    public function logoutSubmit(Request $request, $domain)
    {
        Auth::guard('customer')->logout();
        return redirect()->route('customer.login', getParam());
    }
}
