<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\User;
use App\Services\Mail\MailFromTenantWebsite;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CustomerMailVerify  
{
    use Dispatchable  ;

    private $tenant;
    private $customer;
    private $verifyRoute;
    /**
     * Create a new job instance.
     */
    public function __construct(User $tenant, Customer $customer, $verifyRoute)
    {
        $this->tenant = $tenant;
        $this->customer = $customer;
        $this->verifyRoute = $verifyRoute;
    }

    /**
     * Execute the job.
     */
    public function handle(MailFromTenantWebsite $mailService): void
    {
        try {
            $tenantId = $this->tenant->id;

            $template = $mailService->getMailTemplate($tenantId, 'verify_email');
            $tenantBasicInfo = $mailService->getTenantBasicInfo($tenantId);
            // $verifyRoute = "<a href=" . $this->verifyRoute . ">Click Here</a>";

            $relaceBodyData =  [
                'username' =>  $this->customer->username,
                'verification_link' => $this->verifyRoute,
                'website_title' => $tenantBasicInfo->website_title,
            ];
            $mailBody = $mailService->prepareMailBody($template->mail_body, $relaceBodyData);

            $mailInfo = [
                'to' =>  $this->customer->email,
                'subject' => $template->mail_subject,
                'body' => $mailBody,
            ];

            $mailService->sendMail($tenantId, $mailInfo);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
