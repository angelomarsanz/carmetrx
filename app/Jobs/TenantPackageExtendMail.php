<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Mail\MailFromSuperAdmin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TenantPackageExtendMail 
// implements ShouldQueue
{
    use Dispatchable;
    // , InteractsWithQueue, Queueable, SerializesModels;
    protected $tenant;
    protected $packageTitle;
    protected $packagePrice;
    protected $activation_date;
    protected $expire_date;
    protected $invoice;
    /**
     * Create a new job instance.
     */
    public function __construct(User $tenant, $packageTitle, $packagePrice, $activation_date, $expire_date, $invoice)
    {
        $this->tenant = $tenant;
        $this->packageTitle = $packageTitle;
        $this->packagePrice = $packagePrice;
        $this->activation_date = $activation_date;
        $this->expire_date = $expire_date;
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     */
    public function handle(MailFromSuperAdmin $mailService): void
    {
        try {

            $template = $mailService->getMailTemplate('membership_extend');
            $adminBasicInfo = $mailService->getAdmintBasicInfo();

            $relaceBodyData =  [
                'username' =>  $this->tenant->username,
                'package_title' => $this->packageTitle,
                'package_price' => $this->packagePrice,
                'activation_date' => $this->activation_date,
                'expire_date' => $this->expire_date,
                'website_title' => $adminBasicInfo['website_title'],
            ];
            $mailBody = $mailService->prepareMailBody($template->email_body, $relaceBodyData);

            $mailInfo = [
                'to' =>  $this->tenant->email,
                'subject' => $template->email_subject,
                'body' => $mailBody,
                'invoice' => $this->invoice,
            ];

            $mailService->sendMail($mailInfo);
        } catch (\Exception $e) {
            Log::error("Tenant Packge  Extend Job Error: " . $e->getMessage());
            return;
        }
    }
}
