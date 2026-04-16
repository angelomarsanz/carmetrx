<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Mail\MailFromSuperAdmin;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class TenantVerificationMail
{
    use Dispatchable;

    protected $tenant;
    protected $route;

    /**
     * Create a new job instance.
     * 
     * @param  string  $route  example: '<a href='example.com/resetpassword'>Click Here</a>' or example.com/resetpassword
     * @param     User $tenant   
     */
    public function __construct(User $tenant, string $route)
    {
        $this->tenant = $tenant;
        $this->route = $route;
    }

    /**
     * Execute the job.
     */
    public function handle(MailFromSuperAdmin $mailService): void
    {
        try {

            $template = $mailService->getMailTemplate('email_verification');
            $adminBasicInfo = $mailService->getAdmintBasicInfo();

            $relaceBodyData =  [
                'customer_name' =>  $this->tenant->username,
                'verification_link' => $this->route,
                'website_title' => $adminBasicInfo['website_title'],
            ];
            $mailBody = $mailService->prepareMailBody($template->email_body, $relaceBodyData);

            $mailInfo = [
                'to' =>  $this->tenant->email,
                'subject' => $template->email_subject,
                'body' => $mailBody,
            ];

            $mailService->sendMail($mailInfo);
        } catch (\Exception $e) {
            Log::error("TenantVerificationMail Job Error: " . $e->getMessage());
            return;
        }
    }
}
