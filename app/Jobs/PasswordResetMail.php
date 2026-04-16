<?php

namespace App\Jobs;

use App\Http\Helpers\BasicMailer;
use App\Models\User;
use App\Models\User\BasicSetting;
use App\Models\User\MailTemplate;
use App\Services\Mail\MailFromTenantWebsite;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PasswordResetMail
{
    use Dispatchable;

    protected $recipient;
    protected $route;


    /**
     * Create a new job instance.
     *
     * @param -  \App\Models\User or \App\Models\User\Agent  $recipient  // Assuming both Agent and User  
     * @param  string  $route  example: '<a href='example.com/resetpassword'>Click Here</a>' or example.com/resetpassword
     * @param  string  $type  // 'agent' or 'user'
     */
    public function __construct($recipient, $route)
    {
        $this->recipient = $recipient;
        $this->route = $route;
    }

    /**
     * Execute the job.
     */
    public function handle(MailFromTenantWebsite $mailService): void
    {

        try {
            $tenantId = $this->recipient->user_id;
            $template = $mailService->getMailTemplate($tenantId, 'reset_password');
            $tenantBasicInfo = $mailService->getTenantBasicInfo($tenantId);


            $relaceBodyData =  [
                'customer_name' =>  $this->recipient->username,
                'password_reset_link' => $this->route,
                'website_title' => $tenantBasicInfo->website_title,
            ];
            $mailBody = $mailService->prepareMailBody($template->mail_body, $relaceBodyData);

            $mailInfo = [
                'to' =>  $this->recipient->email,
                'subject' => $template->mail_subject,
                'body' => $mailBody,
            ];

            $mailService->sendMail($tenantId, $mailInfo);
        } catch (\Exception $e) {
            Log::error("PasswordResetMail Job Error: " . $e->getMessage());
            return;
        }
        return;
    }
}
