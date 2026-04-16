<?php

namespace App\Http\Controllers\Front;

use Exception;
use Mpdf\Mpdf;
use Carbon\Carbon;
use App\Models\Faq;
use App\Models\Seo;
use App\Models\Blog;
use App\Models\Page;
use App\Models\User;
use App\Models\Feature;
use App\Models\Package;
use App\Models\Partner;
use App\Models\Process;
use App\Models\Language;
use App\Jobs\MailToAdmin;
use App\Models\Bcategory;
use App\Models\Membership;
use App\Models\Subscriber;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Traits\CustomSection;
use App\Models\BasicSetting as BS;
use App\Models\OfflineGateway;
use App\Models\PaymentGateway;
use App\Models\BasicExtended as BE;
use App\Traits\FrontendLanguage;
use App\Models\AdditionalSection;
use App\Models\User\BasicSetting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class FrontendController extends Controller
{
    use FrontendLanguage, CustomSection;

    public function index()
    {
        if (session()->has('frontend_lang')) {
            $currentLang = $this->selectLang(session()->get('frontend_lang'));
        } else {
            $currentLang = $this->defaultLang();
        }
        $lang_id = $currentLang->id;
        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;
        $data['processes'] = Process::where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();
        $data['features'] = Feature::where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();

        $data['featured_users'] = User::where([
            ['featured', 1],
            ['show_profile_on_admin_website', 1],
            ['status', 1]
        ])
            ->whereHas('memberships', function ($q) {
                $q->where('status', '=', 1)
                    ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
                    ->where('expire_date', '>=', Carbon::now()->format('Y-m-d'));
            })->get();
        $data['testimonials'] = Testimonial::where('language_id', $lang_id)
            ->orderBy('serial_number', 'ASC')
            ->get();
        $data['blogs'] = Blog::where('language_id', $lang_id)->orderBy('serial_number', 'asc')->take(3)->get();

        $data['partners'] = Partner::where('language_id', $lang_id)
            ->orderBy('serial_number', 'ASC')
            ->get();

        $data['templates'] = DB::table('themes')->where('is_active', 1)->orderBy('serial_number', 'ASC')->get();

        $data['seo'] = Seo::where('language_id', $lang_id)->first();

        $sections = CustomSection::AdminFrontHomePage();


        foreach ($sections as $section) {
            $data["after_" . str_replace('_section', '', $section)] = AdditionalSection::where('possition', $section)
                ->where('page_type', 'home')
                ->orderBy('serial_number', 'asc')
                ->get();
        }
        $sectionInfo = BS::select('additional_section_status')->first();
        if (!empty($sectionInfo->additional_section_status)) {
            $info = json_decode($sectionInfo->additional_section_status, true);
            $data['homecusSec'] = $info;
        }

        return view('front.index', $data);
    }

    public function aboutUs()
    {

        if (session()->has('frontend_lang')) {
            $currentLang = $this->selectLang(session()->get('frontend_lang'));
        } else {
            $currentLang = $this->defaultLang();
        }

        $lang_id = $currentLang->id;
        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;

        $data['processes'] = Process::where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();
        $data['features'] = Feature::where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();
        $data['testimonials'] = Testimonial::where('language_id', $lang_id)
            ->orderBy('serial_number', 'ASC')
            ->get();

        $sections = CustomSection::AdminFrontAboutPage();


        foreach ($sections as $section) {
            $data["after_" . str_replace('_section', '', $section)] = AdditionalSection::where('possition', $section)
                ->where('page_type', 'about')
                ->orderBy('serial_number', 'asc')
                ->get();
        }
        $sectionInfo = BS::select('about_additional_section_status')->first();
        if (!empty($sectionInfo->about_additional_section_status)) {
            $info = json_decode($sectionInfo->about_additional_section_status, true);
            $data['aboutcusSec'] = $info;
        }



        return view('front.about-us', $data);
    }

    public function subscribe(Request $request)
    {


        $bs = BS::select('is_recaptcha')->first();

        $rules['email'] = 'required|email|unique:subscribers';
        if ($bs->is_recaptcha == 1) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }
        $messages = [
            'g-recaptcha-response.required' => __('Please verify that you are not a robot'),
            'g-recaptcha-response.captcha' => __('Captcha error! try again later or contact site admin') . '.',
        ];
        $request->validate($rules, $messages);

        try {
            $subsc = new Subscriber;
            $subsc->email = $request->email;
            $subsc->save();
            Session::flash('success', __('Subscribed successfully') . '!');
        } catch (Exception $e) {
            Session::flash('warning', __('Somethinbg went wrong') . '!');
        }


        return redirect()->back();
    }

    public function loginView()
    {
        return view('front.login');
    }

    public function checkUsername($username)
    {
        $count = User::where('username', $username)->count();

        $status = $count > 0;
        return response()->json($status);
    }

    public function step1($status, $id)
    {
        Session::forget('coupon');
        Session::forget('coupon_amount');

        if (Auth::check()) {
            return redirect()->route('user.plan.extend.index');
        }

        $data['status'] = $status;
        $data['id'] = $id;
        $package = Package::findOrFail($id);
        $data['package'] = $package;

        $hasSubdomain = false;
        $features = [];
        if (!empty($package->features)) {
            $features = json_decode($package->features, true);
        }
        if (is_array($features) && in_array('Subdomain', $features)) {
            $hasSubdomain = true;
        }

        $data['hasSubdomain'] = $hasSubdomain;
        return view('front.step', $data);
    }

    public function step2(Request $request)
    {
        $data = $request->session()->get('data');
        $data = $request->session()->get('data');
        if (session()->has('coupon_amount')) {
            $data['cAmount'] = session()->get('coupon_amount');
        } else {
            $data['cAmount'] = 0;
        }
        return view('front.checkout', $data);
    }

    public function checkout(Request $request)
    {

        $this->validate($request, [
            'username' => 'required|regex:/^[^0-9]/|alpha_num|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed'
        ], [
            'username.regex' => __('Username first letter must be a string'),
        ]);
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        email_collector_api($request->email);
        $seo = Seo::where('language_id', $currentLang->id)->first();
        $be = $currentLang->basic_extended;
        $data['bex'] = $be;
        $data['username'] = $request->username;
        $data['email'] = $request->email;
        $data['password'] = $request->password;
        $data['status'] = $request->status;
        $data['id'] = $request->id;
        $online = PaymentGateway::query()->where('status', 1)->get();
        $offline = OfflineGateway::where('status', 1)->get();
        $data['offline'] = $offline;
        $data['payment_methods'] = $online->merge($offline);
        $data['package'] = Package::query()->findOrFail($request->id);
        $data['seo'] = $seo;
        $request->session()->put('data', $data);
        return redirect()->route('front.registration.step2');
    }


    // packages start
    public function pricing(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = $this->selectLang(session()->get('lang'));;
        } else {
            $currentLang = $this->defaultLang();
        }
        $data['seo'] = Seo::where('language_id', $currentLang->id)->first();

        $data['bex'] = BE::first();
        $data['abs'] = BS::first();

        return view('front.pricing', $data);
    }

    // blog section start
    public function blogs(Request $request)
    {

        if (session()->has('frontend_lang')) {
            $currentLang = $this->selectLang(session()->get('frontend_lang'));
        } else {
            $currentLang = $this->defaultLang();
        }

        $data['seo'] = Seo::where('language_id', $currentLang->id)->first();

        $data['currentLang'] = $currentLang;

        $lang_id = $currentLang->id;


        $term = $request->search;
        $category_id = $request->category;

        $data['bcats'] = Bcategory::where('language_id', $lang_id)
            ->where('status', 1)
            ->orderBy('serial_number', 'ASC')
            ->get();

        $data['blogs'] = Blog::when($term, function ($query, $term) {
            return $query->where('title', 'like', '%' . $term . '%');
        })->when($currentLang, function ($query, $currentLang) {
            return $query->where('language_id', $currentLang->id);
        })->when($category_id, function ($query, $category_id) {
            return $query->where('bcategory_id', $category_id);
        })->orderBy('serial_number', 'ASC')
            ->paginate(15);
        return view('front.blogs', $data);
    }

    public function blogdetails($slug, $id)
    {


        if (session()->has('frontend_lang')) {
            $currentLang = $this->selectLang(session()->get('frontend_lang'));
        } else {
            $currentLang = $this->defaultLang();
        }

        $lang_id = $currentLang->id;

        $data['blog'] = Blog::findOrFail($id);
        $data['bcats'] = Bcategory::where('status', 1)
            ->where('language_id', $lang_id)
            ->orderBy('serial_number', 'ASC')
            ->get();


        $data['allBlogs'] = Blog::with('information')->where('language_id', $lang_id)->orderBy('id', 'DESC')->limit(5)->get();
        return view('front.blog-details', $data);
    }

    public function contactView()
    {
        if (session()->has('frontend_lang')) {
            $currentLang = $this->selectLang(session()->get('frontend_lang'));
        } else {
            $currentLang = $this->defaultLang();
        }

        $data['seo'] = Seo::where('language_id', $currentLang->id)->first();
        return view('front.contact', $data);
    }

    public function faqs()
    {
        if (session()->has('frontend_lang')) {
            $currentLang = $this->selectLang(session()->get('frontend_lang'));
        } else {
            $currentLang = $this->defaultLang();
        }
        $data['seo'] = Seo::where('language_id', $currentLang->id)->first();

        $lang_id = $currentLang->id;
        $data['faqs'] = Faq::where('language_id', $lang_id)
            ->orderBy('serial_number', 'ASC')
            ->get();
        return view('front.faq', $data);
    }

    public function dynamicPage($slug)
    {
        $data['page'] = Page::where('slug', $slug)->firstOrFail();
        return view('front.dynamic', $data);
    }

    public function users(Request $request)
    {

        if (session()->has('frontend_lang')) {
            $currentLang = $this->selectLang(session()->get('frontend_lang'));
        } else {
            $currentLang = $this->defaultLang();
        }

        $data['seo'] = Seo::where('language_id', $currentLang->id)->first();
        $search = $request->input('search');

        $users = User::where('online_status', 1)
            ->where('status', 1)
            ->where('show_profile_on_admin_website', 1)
            ->whereHas('memberships', function ($q) {
                $q->where('status', '=', 1)
                    ->where('start_date', '<=', Carbon::now()->format('Y-m-d'))
                    ->where('expire_date', '>=', Carbon::now()->format('Y-m-d'));
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                        ->orWhere('username', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('id', 'DESC')
            ->paginate(9);

        $data['users'] = $users;
        return view('front.users', $data);
    }




    public function paymentInstruction(Request $request)
    {
        $offline = OfflineGateway::where('name', $request->name)
            ->select('short_description', 'instructions', 'is_receipt')
            ->first();

        return response()->json([
            'description' => $offline->short_description,
            'instructions' => $offline->instructions,
            'is_receipt' => $offline->is_receipt
        ]);
    }


    public function adminContactMessage(Request $request)
    {
        $request->all();
        $rules = [
            'name' => 'required',
            'email' => 'required|email:rfc,dns',
            'subject' => 'required',
            'message' => 'required'
        ];

        $bs = BS::select('is_recaptcha')->first();

        if ($bs->is_recaptcha == 1) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }
        $messages = [
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
        ];

        $request->validate($rules, $messages);

        $data['fromMail'] = $request->email;
        $data['fromName'] = $request->name;
        $data['subject'] = $request->subject;
        $data['body'] = $request->message;

        MailToAdmin::dispatch($data);
        Session::flash('success', 'Message sent successfully');
        return back();
    }

    public function contact(Request $request, $domain)
    {
        $user = getUser();

        $language =  $this->getUserCurrentLanguage($user->id);

        $queryResult['seoInfo'] = \App\Models\User\SEO::query()->where('user_id', $user->id)->select('contact_meta_keywords', 'contact_meta_description')->first();

        $queryResult['pageHeading'] = $this->getUserPageHeading($language, $user->id);

        $queryResult['bgImg'] = $this->getUserBreadcrumb($user->id);

        $queryResult['info'] = BasicSetting::query()
            ->where('user_id', $user->id)
            ->select('email_address', 'contact_number', 'address', 'latitude', 'longitude')
            ->firstOrFail();
        return view('user-front.common.contact', $queryResult);
    }

    public function userFaqs($domain)
    {
        $user = getUser();

        $language = $this->getUserCurrentLanguage($user->id);

        $queryResult['seoInfo'] = \App\Models\User\SEO::query()->where('user_id', $user->id)->select('faqs_meta_keywords', 'faqs_meta_description')->first();

        $queryResult['pageHeading'] = $this->getUserPageHeading($language, $user->id);

        $queryResult['bgImg'] = $this->getUserBreadcrumb($user->id);

        $queryResult['faqs'] = User\FAQ::query()->where('user_id', $user->id)
            ->where('language_id', $language->id)
            ->orderBy('serial_number', 'ASC')
            ->get();

        return view('user-front.common.faqs', $queryResult);
    }




    public function changeLanguage($lang): \Illuminate\Http\RedirectResponse
    {
        session()->put('frontend_lang', $lang);
        app()->setLocale($lang);
        return redirect()->route('front.index');
    }


    public function removeMaintenance($domain, $token)
    {
        Session::put('user-bypass-token', $token);
        return redirect()->route('front.user.detail.view', getParam());
    }

    public function userCPage($domain, $slug)
    {
        $user = getUser();

        $language = $this->getUserCurrentLanguage($user->id);

        $queryResult['bgImg'] = $this->getUserBreadcrumb($user->id);

        $queryResult['pageInfo'] = User\CustomPage\Page::query()
            ->join('user_page_contents', 'user_pages.id', '=', 'user_page_contents.page_id')
            ->where('user_pages.status', '=', 1)
            ->where('user_page_contents.language_id', '=', $language->id)
            ->where('user_page_contents.slug', '=', $slug)
            ->firstOrFail();

        return view('user-front.common.custom-page', $queryResult);
    }
}
