<?php

namespace App\Http\Controllers\UserFrontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\MailFromUserRequest;
use App\Jobs\MailToTenant;
use App\Models\User\BasicSetting;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use App\Traits\Tenant\Frontend\PageHeadings;
use App\Traits\Tenant\Frontend\Language as TenantFrontendLanguage;
use Illuminate\Support\Facades\Session;

class ContactController extends Controller
{
    use TenantFrontendLanguage, PageHeadings;
    public function contact()
    {
        $misc = new MiscellaneousController();
        $tenantId = getUser()->id;
        $language = $this->currentLang($tenantId);
        $queryResult['pageHeading'] = $this->pageHeading($tenantId);

        $queryResult['seoInfo'] = $language->seoInfo()->select('meta_keyword_contact', 'meta_description_contact')->first();


        $queryResult['breadcrumb'] = $misc->getBreadcrumb($tenantId);

        $queryResult['info'] = BasicSetting::where('user_id', $tenantId)->select('email_address', 'contact_number', 'address', 'google_recaptcha_status', 'latitude', 'longitude')->first();

        return view('tenant_frontend.contact', $queryResult);
    }

    public function sendMail(MailFromUserRequest $request)
    {
        $tenantId = getUser()->id;
        $data['fromMail'] = $request->email;
        $data['fromName'] = $request->name;
        $data['subject'] = $request->subject;
        $data['body'] = $request->message;

        MailToTenant::dispatch($tenantId, $data);

        Session::flash('success', __('Message sent successfully!'));

        return redirect()->back();
    }
}
