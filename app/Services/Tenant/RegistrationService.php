<?php

namespace App\Services\Tenant;

use App\Models\User;
use App\Models\Language;
use App\Models\Package;
use App\Models\Membership;
use App\Models\Coupon;
use App\Http\Helpers\UserPermissionHelper;
use App\Jobs\TenantPackagePurchaseMail;
use App\Jobs\TenantVerificationMail;
use App\Models\BasicSetting;
use App\Models\User\BasicSetting as UserBasicSetting;
use App\Models\User\HomePage\Section;
use App\Models\User\HomePage\SectionTitle;
use App\Models\User\Language as UserLanguage;
use App\Models\User\MailTemplate;
use App\Models\User\Menu as UserMenu;
use App\Models\User\UserPermission;
use App\Models\User\UserSubdomain;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Mpdf\Mpdf;

class RegistrationService
{
  public function register($data)
  {

    try {
      DB::beginTransaction();
      // Create user
      $token = md5(time() . $data['username'] . $data['email']);
      if (isset($data['mode'])) {
        $click_here = __('Click Here');

        $data['token'] = $token;
        $rotue = route('user-register-token', ['mode' => $data['mode'], 'token' => $token]);
        $verification_link = "<a href='{$rotue}'> <button type='button' class='btn btn-primary'>{$click_here}</button></a>";
      }


      //register tenant
      $Tenant = new User();
      $tenant =  $Tenant->register($data);

      // register membership
      $membership = new Membership();

      $member = $membership->register($tenant->id, $data);



      $this->initializeDefaults($tenant);
      $this->storeFeatures($tenant->id, $data['package_id']);
      $this->couponCount();
      // Create a invoice
      $invoice =  $this->makeInvoice($tenant, $member, $data['package_title'], $data['website_title']);

      // Send mail with invoice
      $this->sendMailWithInvoice($tenant,  $data, $invoice);
      DB::commit();
      if (isset($data['mode'])) {

        //  Send verification email
        if ($member->payment_method != 'Iyzico') {
          $this->sendVerificationEmail($tenant, $verification_link);
        }
      }
    } catch (Exception $e) {
      DB::rollBack();
      Log::error('Tenant registration failed', ['error' => $e->getMessage()]);
      // throw new Exception('Failed to register tenant.', 0, $e);
    }
  }

  /**
   * Initialize default settings for a newly registered user.
   *
   * @param User $tenant
   */
  public function initializeDefaults(User $tenant)
  {
    $adminlangs = Language::get();

    foreach ($adminlangs as $lang) {
      $userLang = $this->storeLanguage($tenant->id, $lang);
      $this->storeMenus($tenant->id, $userLang->id);
      $this->storeSectionTitle($tenant->id, $userLang->id);
    }
    $this->storeBasicInfo($tenant);
    $this->storeSectionInfo($tenant->id);
    $this->insertMailTemplate($tenant->id);
    $this->insertSubdomain($tenant);
  }

  /**
   * Store in tenant language
   *
   * @param int $tenantId 
   * @param  Language $lang (admin language)
   */
  private function storeLanguage($tenantId, Language $lang)
  {
    $userLang = new UserLanguage();

    $userLang->user_id = $tenantId;
    $userLang->name = $lang->name;
    $userLang->code =  $lang->code;
    $userLang->is_default = $lang->is_default;
    $userLang->is_admin = 1;
    $userLang->rtl = $lang->rtl;
    $userLang->keywords = $lang->user_front_keywords;
    $userLang->save();



    return $userLang;
  }

  /**
   * Store in tenant menus from default config file (language wise)
   *
   * @param User $tenant 
   */
  private function storeMenus($tenantId, $langId)
  {
    $menus = Config::get('defaults.menus');
    $tenantWebMenu = new UserMenu();
    $tenantWebMenu->store($tenantId, $langId, $menus);
  }

  /**
   * Store in database tenant basic settings information
   *
   * @param User $tenant 
   */
  protected function storeBasicInfo(User $tenant)
  {

    UserBasicSetting::create([
      'theme_version' => 1,
      'user_id' => $tenant->id,
      'maintenance_status' => 0,
      'website_title' => $tenant->company_name,
      'email' => $tenant->email,
      'from_name' => $tenant->company_name,
    ]);
  }

  /**
   * Store in database tenant mail template information from default config file
   *
   * @param $tenantId 
   */
  protected function insertMailTemplate($tenantId)
  {

    $mailTemplates = Config::get('defaults.mailTemplates');
    $mailTlemplate = new MailTemplate();
    foreach ($mailTemplates as $template) {
      $mailTlemplate->store($tenantId, $template);
    }
  }

  /**
   * Store in database tenant subdomain package permmission wise
   *
   * @param User $tenant  
   */
  protected function insertSubdomain(User $tenant)
  {
    $packagePermission = UserPermissionHelper::packagePermission($tenant->id);
    if (!empty($packagePermission)) {
      $permissions = json_decode($packagePermission, true);
      $userCurrentPackage =  UserPermissionHelper::currentPackage($tenant->id);
      $subdomainLeft = $userCurrentPackage->subdomain_limit ?? 0;
      if (!empty($permissions) && in_array('Subdomain', $permissions) && $subdomainLeft > 0) {

        $subdomain = new UserSubdomain();
        $subdomain->store($tenant->id, $tenant->username);
      }
    }
  }

  /**
   * Store in permission table tenant package features 
   *
   * @param $tenantId  
   * @param $packageId  
   */
  public function storeFeatures($tenantId, $packageId)
  {
    $package = Package::findOrFail($packageId);

    $features = json_decode($package->features, true);
    $features[] = "Contact";
    $permission = new UserPermission();
    $permission->store($tenantId, $packageId, $features);
  }

  /**
   * If user has coupon it will be countable and forget session
   * 
   */
  protected function couponCount()
  {
    if (Session::has('coupon')) {
      $coupon = Coupon::where('code', Session::get('coupon'))->first();
      $coupon->total_uses = $coupon->total_uses + 1;
      $coupon->save();
      Session::forget('coupon');
    }
  }
  /**
   * Store tenant defalt setion
   * @param $tenantId
   * 
   */
  private function storeSectionInfo($teantId)
  {
    $section = new Section();
    $section->updateOrCreateSection($teantId);
  }

  /**
   * Store tenant defalt setion title
   * @param $tenantId
   * @param $langId
   * 
   */
  private function storeSectionTitle($teantId, $langId)
  {
    $section = new SectionTitle();
    $section->updateOrCreateSectionTitle($teantId, $langId);
  }

  /**
   * Send a verification email to the tenant.
   *
   * @param User $tenant
   * @param string $verificationLink
   */
  private function sendVerificationEmail(User $tenant, string $verificationLink)
  {

    dispatch_sync(new TenantVerificationMail($tenant, $verificationLink));
  }

  /**
   * Generate a PDF invoice for the membership.
   * @param User $tenant
   * @param Membership $membership
   * @param  $packageTitle
   * @param $websiteTitle
   */

  public function makeInvoice($tenant, $membership, $packageTitle, $websiteTitle)
  {

    if ($membership->payment_method == 'Iyzico') {
      return null;
    }
    $fileName = uniqid('TMI') . ".pdf";
    $invoiceDir = public_path('assets/front/invoices/');
    $pathName = $invoiceDir . $fileName;

    @mkdir($invoiceDir, 0775, true);

    $bs = BasicSetting::select('logo')->first();
    $logoPath = public_path('assets/front/img/' . ($bs->logo ?? 'noimage.jpg'));
    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));

    // Render Blade view to HTML
    $html = view('pdf.membership', compact('tenant', 'membership', 'packageTitle', 'websiteTitle', 'logoBase64'))->render();

    // Initialize mPDF with RTL and UTF-8 support
    $mpdf = new Mpdf([
      'mode' => 'utf-8',
      'format' => 'A4',
      'default_font' => 'dejavusans',
      'autoScriptToLang' => true,
      'autoLangToFont' => true,
    ]);

    // Write HTML content to PDF
    $mpdf->WriteHTML($html);

    // Save PDF file
    $mpdf->Output($pathName, \Mpdf\Output\Destination::FILE);

    return $pathName;
  }

  /**
   * Send email to the tenant.
   *
   * @param User $tenant
   * @param $data
   * @param $invoice
   */
  public function sendMailWithInvoice(User $tenant, $data, $invoice)
  {
    if ($invoice == null) {
      return;
    }
    $packageTitle = $data['package_title'];
    $packagePrice = $data['package_price'];
    $activation_date = $data['start_date'];
    $total = $data['price'];
    $discount = $data['discount'] ?? 0;
    $expire_date = Carbon::parse($data['expire_date'])->format('Y') == '9999' ? 'Lifetime' : $data['expire_date'];

    dispatch_sync(new TenantPackagePurchaseMail($tenant, $total, $discount, $packageTitle, $packagePrice, $activation_date, $expire_date, $invoice));
  }
}
