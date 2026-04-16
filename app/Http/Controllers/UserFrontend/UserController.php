<?php

namespace App\Http\Controllers\UserFrontend;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Http\Requests\UserFrontend\LoginRequest;
use App\Http\Requests\UserFrontend\SignupRequest;
use App\Http\Requests\UserFrontend\UpdatePasswordRequest;
use App\Http\Requests\UserFrontend\UpdateProfileRequest;
use App\Jobs\CustomerMailVerify;
use App\Jobs\PasswordResetMail;
use App\Models\Customer;
use App\Models\User;
use App\Models\User\BasicSetting;
use App\Models\User\Project\Project;
use App\Models\User\Project\Wishlist as ProjectWishlist;
use App\Models\User\Property\Property;
use App\Models\User\Property\Wishlist;
use App\Rules\MatchEmailRule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Traits\Tenant\Frontend\PageHeadings;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
    use TenantFrontendLanguage, PageHeadings;
    public function login()
    {
        $tenantId = getUser()->id;
        $misc = new MiscellaneousController();

        $language = $this->currentLang($tenantId);

        $queryResult['seoInfo'] = $language->seoInfo($tenantId)->select('meta_keyword_login', 'meta_description_login')->first();

        $queryResult['pageHeading'] = $this->pageHeading($tenantId);

        $queryResult['breadcrumb'] = $misc->getBreadcrumb($tenantId);


        return view('tenant_frontend.login', $queryResult);
    }


    public function redirectToGoogle()
    {
        $tenantId = getUser()->id;
        $bs = BasicSetting::where([['user_id', $tenantId]])->select('google_login_status', 'google_client_id', 'google_client_secret')->first();

        Config::set('services.google.client_id', $bs->google_client_id);
        Config::set('services.google.client_secret', $bs->google_client_secret);
        Config::set('services.google.redirect', route('frontend.user.login.google_callback', getParam()));

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        return $this->authenticationViaProvider('google');
    }

    public function authenticationViaProvider($driver)
    {
        // get the url from session which will be redirect after login
        if (Session::has('redirectTo')) {
            $redirectURL = Session::get('redirectTo');
        } else {
            $redirectURL = route('user.dashboard');
        }

        $responseData = Socialite::driver($driver)->user();
        $userInfo = $responseData->user;

        $isUser = User::query()->where('email_address', '=', $userInfo['email'])->first();

        if (!empty($isUser)) {
            // log in
            if ($isUser->status == 1) {
                Auth::login($isUser);

                return redirect($redirectURL);
            } else {
                Session::flash('error', 'Sorry, your account has been deactivated.');

                return redirect()->route('user.login');
            }
        } else {
            // get user avatar and save it
            $avatar = $responseData->getAvatar();
            $fileContents = file_get_contents($avatar);

            $avatarName = $responseData->getId() . '.jpg';
            $path = public_path('assets/img/users/');

            file_put_contents($path . $avatarName, $fileContents);

            // sign up
            $user = new User();

            if ($driver == 'facebook') {
                $user->first_name = $userInfo['name'];
            } else {
                $user->first_name = $userInfo['given_name'];
                $user->last_name = $userInfo['family_name'];
            }

            $user->image = $avatarName;
            $user->email_address = $userInfo['email'];
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->status = 1;
            $user->provider = ($driver == 'facebook') ? 'facebook' : 'google';
            $user->provider_id = $userInfo['id'];
            $user->save();

            Auth::login($user);

            return redirect($redirectURL);
        }
    }

    public function loginSubmit(LoginRequest $request)
    {
        // Retrieve the redirect URL from the session or set a default route
        $redirectURL = $request->session()->pull('redirectTo', route('frontend.user.dashboard', getParam()));

        // get the email-address and password which has provided by the user
        $credentials = $request->only('username', 'password');

        // login attempt
        if (Auth::guard('customer')->attempt($credentials)) {
            $authCustomer = Auth::guard('customer')->user();

            // Check if the user's email is verified
            if (!$authCustomer->isVerified()) {
                return $this->handleFailedLogin('Please verify your email address.');
            }
            // Check if the user's account is active
            if (!$authCustomer->isActive()) {
                return $this->handleFailedLogin('Sorry, your account has been deactivated.');
            }
            // Redirect the authenticated user to the appropriate URL
            return redirect($redirectURL);
        } else {

            session()->flash('error', 'Incorrect username or password!');
            return redirect()->back();
        }
    }
    /**
     * Handle login failure due to verification or deactivation issues.
     *
     * @param string $message 
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleFailedLogin(string $message)
    {
        session()->flash('error', $message);
        Auth::guard('customer')->logout();
        return redirect()->back();
    }

    public function forgetPassword($username)
    {
        $tenantId = getUser()->id;
        $misc = new MiscellaneousController();

        $language = $this->currentLang($tenantId);
        $queryResult['pageHeading'] = $this->pageHeading($tenantId);



        $queryResult['breadcrumb'] = $misc->getBreadcrumb($tenantId);

        return view('tenant_frontend.forget-password', $queryResult);
    }

    public function forgetPasswordMail($username, Request $request)
    {
        $rules = [
            'email' => [
                'required',
                'email:rfc,dns',
                new MatchEmailRule('customer')
            ]
        ];
        $tenantId = getUser()->id;
        $info = BasicSetting::where('user_id', $tenantId)->select('google_recaptcha_status')->first();
        if ($info->google_recaptcha_status == 1) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $messages = [];

        if ($info->google_recaptcha_status == 1) {
            $messages['g-recaptcha-response.required'] = 'Please verify that you are not a robot.';
            $messages['g-recaptcha-response.captcha'] = 'Captcha error! try again later or contact site admin.';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $customer = Customer::where('email', $request->email)->firstOrFail();
        $token =  Str::random(32);
        $customer->reset_token = $token;
        $customer->save();

        $route = route('frontend.user.reset_password', [getParam(), 'token' => $token]);

        $routeLink  = "<a href=" . $route . "> Click Here</a>";
        PasswordResetMail::dispatchSync($customer, $routeLink);
        return redirect()->back();
    }



    public function resetPassword($website, $token)
    {
        $userId = getUser()->id;

        $customer = Customer::where('reset_token', $token)->firstOrFail();

        $misc = new MiscellaneousController();

        $language = $misc->getLanguage();

        $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_forget_password', 'meta_description_forget_password')->first();

        $queryResult['token'] = $customer->reset_token;
        $queryResult['breadcrumb'] = $misc->getBreadcrumb($userId);

        return view('tenant_frontend.reset-password', $queryResult);
    }

    public function resetPasswordSubmit(Request $request)
    {
        $request->validate(
            [

                'new_password' => 'required|min_digits:6|confirmed',
                'new_password_confirmation' => 'required',
            ],
            [
                'new_password.confirmed' => 'Password confirmation failed.',
                'new_password_confirmation.required' => 'The confirm new password field is required.'
            ]
        );

        $customer = Customer::where('reset_token', $request->reset_token)->firstOrFail();
        if ($customer) {

            $customer->update([
                'password' => Hash::make($request->new_password),
                'reset_token' => null
            ]);

            $request->session()->flash('success', __('Password updated successfully.'));
        } else {
            $request->session()->flash('error', __('Something went wrong!'));
        }

        return redirect()->route('frontend.user.login', getParam());
    }

    public function signup($username)
    {
        $tenantId = getUser()->id;
        $misc = new MiscellaneousController();

        $language = $this->currentLang($tenantId);

        $queryResult['seoInfo'] = $language->seoInfo($tenantId)->select('meta_keyword_signup', 'meta_description_signup')->first();

        $queryResult['pageHeading'] = $this->pageHeading($tenantId);

        $queryResult['breadcrumb'] = $misc->getBreadcrumb($tenantId);

        $queryResult['recaptchaStatus'] = BasicSetting::where('user_id', $tenantId)->pluck('google_recaptcha_status')->first();

        return view('tenant_frontend.signup', $queryResult);
    }

    public function signupSubmit($username, SignupRequest $request,)
    {

        try {
            $user = getUser();

            $customer = new Customer();
            $customer = $customer->singup($user->id, $request);
            /**
             * prepare a verification mail and, send it to user to verify his/her email address,
             * get the mail template information from db
             */
            $route = route('frontend.user.verify_email', [getParam(), 'token' => $customer->verification_token]);
            $verifyLink = "<a href='{$route}'>Click Here</a>";

            CustomerMailVerify::dispatch($user, $customer, $verifyLink);

            return redirect()->back()->with('success', 'A verification link has been sent to your email address.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Semething went wrong!');
        }
    }

    public function signupVerify($username, $token)
    {
        try {
            $customer = Customer::verifyEmail($token);
            if ($customer) {
                session()->flash('success', 'Your email address has been verified.');
                // after email verification, authenticate this user
                Auth::guard('customer')->login($customer);
            }

            return redirect()->route('frontend.user.dashboard', getParam());
        } catch (ModelNotFoundException $e) {
            session()->flash('error', 'Could not verify your email address!');

            return redirect()->route('frontend.user.signup', getParam());
        }
    }

    public function redirectToDashboard($username)
    {
        $userId = getUser()->id;
        $misc = new MiscellaneousController();

        $queryResult['breadcrumb'] = $misc->getBreadcrumb($userId);

        $user = Customer::find(Auth::guard('customer')->user()->id);
        $queryResult['authUser'] = $user;

        // $queryResult['numOfServiceOrders'] = $user->serviceOrders($this->websiteId)->count();

        // $queryResult['numOfWishlistedServices'] = $user->ServiceWishlists($this->websiteId)->count();

        // $queryResult['numOfProductOrders'] = $user->productOrders($this->websiteId)->count();

        return view('tenant_frontend.user.dashboard', $queryResult);
    }

    public function editProfile($username)
    {
        $userId = getUser()->id;
        $misc = new MiscellaneousController();

        $queryResult['breadcrumb'] = $misc->getBreadcrumb($userId);

        $user  = Customer::active()->verified()->find(Auth::guard('customer')->user()->id);

        $queryResult['authUser'] =  $user;
        return view('tenant_frontend.user.edit-profile', $queryResult);
    }

    public function updateProfile($username, UpdateProfileRequest $request)
    {
        $customerId = Auth::guard('customer')->user()->id;
        $customer = Customer::findOrFail($customerId);

        $imageName = $customer->image;
        if ($request->hasFile('image')) {
            $newImg = $request->file('image');
            $oldImg = $customer->image;
            $imageName = UploadFile::update(Constant::CUSTOMER_IMAGE . '/', $newImg, $oldImg);
        }
        $customer->updateCustomer($request, $imageName);

        session()->flash('success', 'Your profile has been updated successfully.');

        return redirect()->back();
    }

    public function changePassword($username)
    {
        $userId = getUser()->id;
        $misc = new MiscellaneousController();

        $breadcrumb = $misc->getBreadcrumb($userId);

        return view('tenant_frontend.user.change-password', compact('breadcrumb'));
    }

    public function updatePassword($username, UpdatePasswordRequest $request)
    {

        $customer = Customer::findOrFail(Auth::guard('customer')->user()->id);
        $customer->update([
            'password' => Hash::make($request->new_password)
        ]);

        session()->flash('success', 'Password updated successfully.');

        return redirect()->back();
    }
    public function propertyWishlist($username)
    {
        $userId = getUser()->id;
        $misc = new MiscellaneousController();
        $currentLang = $this->currentLang($userId);
        $queryResult['breadcrumb'] = $misc->getBreadcrumb($userId);

        $customer_id = Auth::guard('customer')->user()->id;
        $wishlists = Wishlist::where('customer_id', $customer_id)
            ->with(['propertyContent' => function ($q) use ($currentLang) {
                $q->where('language_id', $currentLang->id);
            }])
            ->get();
        $information['wishlists'] = $wishlists;
        return view('tenant_frontend.user.property-wishlist', $information);
    }

    public function projectWishlist($username)
    {
        $userId = getUser()->id;
        $misc = new MiscellaneousController();
        $currentLang = $this->currentLang($userId);
        $queryResult['breadcrumb'] = $misc->getBreadcrumb($userId);

        $customer_id = Auth::guard('customer')->user()->id;
        $wishlists = ProjectWishlist::where('customer_id', $customer_id)
            ->with('projectContent')
            ->with(['projectContent' => function ($q) use ($currentLang) {
                $q->where('language_id', $currentLang->id);
            }])
            ->get();
        $information['wishlists'] = $wishlists;
        return view('tenant_frontend.user.project-wishlist', $information);
    }

    //add_to_wishlist

    public function addToWishlist($username, Property $property)
    {
        if ($property && Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            $propertyExists = Wishlist::where('property_id', $property->id)->where('customer_id', $customer->id)->first();

            if (!empty($propertyExists)) {
                return redirect()->back()->with('warning', 'You already added this property into your wishlist..!');
            } else {
                $wishlist =  new Wishlist();
                $wishlist->createWishlist($customer->id, $customer->user_id, $property->id);

                return redirect()->back()->with('success', 'Added to your wishlist successfully.');
            }
        } else {
            session(['redirectTo' => url()->current()]);
            return redirect()->route('frontend.user.login', getParam());
        }
    }
    //remove_wishlist
    public function removeWishlist($username, Property $property)
    {
        if ($property && Auth::guard('customer')->check()) {
            $remove = Wishlist::where([['customer_id', Auth::guard('customer')->user()->id], ['property_id', $property->id]])->firstOrFail();
            if ($remove) {
                $remove->delete();
                $notification = array('success' => __('Removed From wishlist successfully!'));
            } else {
                $notification = array('error' => __('Something went wrong!'));
            }
            return back()->with($notification);
        } else {
            session(['redirectTo' => url()->current()]);
            return redirect()->route('frontend.user.login');
        }
    }

    public function addToProjectWishlist($username, Project $project)
    {
        if ($project && Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            $propertyExists = ProjectWishlist::where('project_id', $project->id)->where('customer_id', $customer->id)->first();

            if (!empty($propertyExists)) {
                return redirect()->back()->with('warning', 'You already added this project into your wishlist..!');
            } else {
                $wishlist =  new ProjectWishlist();
                $wishlist->createWishlist($customer->id, $customer->user_id, $project->id);

                return redirect()->back()->with('success', 'Added to your wishlist successfully.');
            }
        } else {
            session(['redirectTo' => url()->current()]);
            return redirect()->route('frontend.user.login', getParam());
        }
    }
    //remove_wishlist
    public function removeProjectWishlist($username, Project $project)
    {
        if ($project && Auth::guard('customer')->check()) {
            $remove = ProjectWishlist::where([['customer_id', Auth::guard('customer')->user()->id], ['project_id', $project->id]])->firstOrFail();
            if ($remove) {
                $remove->delete();
                $notification = array('success' => __('Removed From wishlist successfully!'));
            } else {
                $notification = array('error' => __('Something went wrong!'));
            }
            return back()->with($notification);
        } else {
            session(['redirectTo' => url()->current()]);
            return redirect()->route('frontend.user.login');
        }
    }

    public function logoutSubmit($username, Request $request)
    {
        Auth::guard('customer')->logout();

        if ($request->session()->has('redirectTo')) {
            $request->session()->forget('redirectTo');
        }
        Session::forget('user_id');
        return redirect()->route('frontend.user.login', getParam());
    }
}
