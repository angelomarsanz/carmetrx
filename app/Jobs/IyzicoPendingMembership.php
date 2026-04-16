<?php

namespace App\Jobs;

use App\Models\Membership;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\BasicSetting;
use App\Models\Package;
use App\Models\PaymentGateway;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class IyzicoPendingMembership implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $memberhip_id;
    /**
     * Create a new job instance.
     */
    public function __construct($memberhip_id)
    {
        $this->memberhip_id = $memberhip_id;
    }


    /**
     * Execute the job.
     */
    public function handle()
    {
        $memberhip = Membership::where('id', $this->memberhip_id)->first();

        Log::info("ok");

        $conversation_id = $memberhip->conversation_id;

        $paymentMethod = PaymentGateway::where('keyword', 'iyzico')->first();
        $paydata = $paymentMethod->convertAutoData();

        $options = new \Iyzipay\Options();
        $options->setApiKey($paydata['api_key']);
        $options->setSecretKey($paydata['secret_key']);
        if ($paydata['sandbox_status'] == 1) {
            $options->setBaseUrl("https://sandbox-api.iyzipay.com");
        } else {
            $options->setBaseUrl("https://api.iyzipay.com"); // production mode
        }

        $request = new \Iyzipay\Request\ReportingPaymentDetailRequest();
        $request->setPaymentConversationId($conversation_id);

        $paymentResponse = \Iyzipay\Model\ReportingPaymentDetail::create($request, $options);
        $result = (array) $paymentResponse;

        // dd($result);
        foreach ($result as $key => $data) {
            $data = json_decode($data, true);
            if ($data['status'] == 'success' && !is_null($data['payments'])) {
                if (is_array($data['payments']) && !empty($data['payments'])) {
                    if ($data['payments'][0]['paymentStatus'] == 1) {
                        //success 
                        $memberhip->status = 1;
                        $memberhip->save();

                        $tenant =  User::where('id', $memberhip->user_id)->first();

                        $package = Package::where('id', $memberhip->package_id)->first();

                        // Create a invoice
                        $invoice =  $this->makeInvoice($tenant, $memberhip, $package->title, 'MultiEstate');

                        // Send mail with invoice
                        $this->sendMailWithInvoice($tenant,  $data, $invoice, $package, $memberhip);
                        DB::commit();

                        $token = md5(time() . $tenant->username . $tenant->email);
                        $click_here = __('Click Here');

                        $data['token'] = $token;
                        $rotue = route('user-register-token', ['mode' => 'online', 'token' => $token]);
                        $verification_link = "<a href='{$rotue}'> <button type='button' class='btn btn-primary'>{$click_here}</button></a>";

                        //  Send verification email
                        $this->sendVerificationEmail($tenant, $verification_link);
                    } else {
                        $memberhip->status = 2;
                        $memberhip->save();
                    }
                } else {
                    $memberhip->status = 2;
                    $memberhip->save();
                }
            } else {
                $memberhip->status = 2;
                $memberhip->save();
            }
        }
    }

    public function makeInvoice($tenant, $membership, $packageTitle, $websiteTitle)
    {
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

    public function sendMailWithInvoice(User $tenant, $data, $invoice, $package, $memberhip)
    {
        $packageTitle = $package->title;
        $packagePrice = $package->price;
        $activation_date = $memberhip->start_date;
        $total = $memberhip->price;
        $discount = $memberhip->discount ?? 0;
        $expire_date = Carbon::parse($memberhip->expire_date)->format('Y') == '9999' ? 'Lifetime' : $memberhip->expire_date;

        dispatch_sync(new TenantPackagePurchaseMail($tenant, $total, $discount, $packageTitle, $packagePrice, $activation_date, $expire_date, $invoice));
    }
    private function sendVerificationEmail(User $tenant, string $verificationLink)
    {

        dispatch_sync(new TenantVerificationMail($tenant, $verificationLink));
    }
}
