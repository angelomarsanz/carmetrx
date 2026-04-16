<?php

namespace App\Jobs;

use App\Services\Mail\MailFromTenantWebsite;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class MailToTenant
{
    use Dispatchable;
    public $data, $tenantId;

    /**
     * Create a new job instance.
     */
    public function __construct($tenantId, $data)
    {
        $this->tenantId = $tenantId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(MailFromTenantWebsite $mailService): void
    {
        try {
            $data = $this->data;
            $basicInfo = $mailService->getTenantBasicInfo($this->tenantId);
            $body = nl2br($data['body']) . "<br/> <br/><strong>Enquirer Name: </strong>" . $data['fromName'] . " <br/>" . "<strong>Enquirer Email: </strong> " . $data['fromMail'];
            $mailInfo = [
                'replayTo' => $data['fromMail'],
                'fromName' => $data['fromName'],
                'to' =>  $basicInfo->email,
                'subject' => $data['subject'],
                'body' => $body,
            ];
            Log::info($basicInfo->email);
            $mailService->sendMail($this->tenantId, $mailInfo);
        } catch (\Exception $e) {
            Log::error("MailToTenant Job Error: " . $e->getMessage());
            return;
        }
        return;
    }
}
