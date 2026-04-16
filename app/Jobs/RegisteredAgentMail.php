<?php

namespace App\Jobs;

use App\Models\User\Agent\Agent;
use App\Services\Mail\MailFromTenantWebsite;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class RegisteredAgentMail
{
    use Dispatchable;
    protected  $agent, $data;
    /**
     * Create a new job instance.
     */
    public function __construct(Agent $agent, $data)
    {
        $this->agent = $agent;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(MailFromTenantWebsite $mailService): void
    {
        try {
            $tenantId = $this->agent->user_id;
            $template = $mailService->getMailTemplate($tenantId, 'agent_register');
            $tenantBasicInfo = $mailService->getTenantBasicInfo($tenantId);


            $relaceBodyData =  [
                'username' =>  $this->agent->username,
                'login_url' => $this->data['login_url'],
                'password' => $this->data['password'],
                'website_title' => $tenantBasicInfo->website_title,
            ];
            $mailBody = $mailService->prepareMailBody($template->mail_body, $relaceBodyData);

            $mailInfo = [
                'to' =>  $this->agent->email,
                'subject' => $template->mail_subject,
                'body' => $mailBody,
            ];

            $mailService->sendMail($tenantId, $mailInfo);
        } catch (\Exception $e) {
            Log::error("Registerd Agent Mail Job Error: " . $e->getMessage());
            return;
        }
        return;
    }
}
