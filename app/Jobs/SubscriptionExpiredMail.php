<?php

namespace App\Jobs;

use App\Http\Helpers\MegaMailer;
use App\Services\Mail\MailFromSuperAdmin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SubscriptionExpiredMail
// implements ShouldQueue
{
    use Dispatchable;
    // , InteractsWithQueue, Queueable, SerializesModels;

    public $tenant;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MailFromSuperAdmin $mailService): void
    {



        try {

            $template = $mailService->getMailTemplate('membership_expired');
            $adminBasicInfo = $mailService->getAdmintBasicInfo();

            $relaceBodyData =  [
                'username' => $this->tenant->username,
                'login_link' => "<a href='" . route('user.login') . "'>" . route('user.login') . "</a>",
                'website_title' => $adminBasicInfo->website_title,
            ];
            $mailBody = $mailService->prepareMailBody($template->email_body, $relaceBodyData);

            $mailInfo = [
                'to' =>  $this->tenant->email,
                'subject' => $template->email_subject,
                'body' => $mailBody,
            ];

            $mailService->sendMail($mailInfo);
        } catch (\Exception $e) {
            Log::error("Tenant Subscription Expired Mail Job Error: " . $e->getMessage());
            return;
        }
    }
}
