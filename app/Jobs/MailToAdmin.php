<?php

namespace App\Jobs;

use App\Services\Mail\MailFromSuperAdmin; 
use Illuminate\Foundation\Bus\Dispatchable; 
use Illuminate\Support\Facades\Log;

class MailToAdmin
{
    use Dispatchable;
    public $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(MailFromSuperAdmin $mailService): void
    {
        try {
            $data = $this->data;
            $basicInfo = $mailService->getAdmintBasicExtend();
            $body = nl2br($data['body']) . "<br/> <br/><strong>Enquirer Name: </strong>" . $data['fromName'] . " <br/>" . "<strong>Enquirer Email: </strong> " . $data['fromMail'];
            $mailInfo = [
                'replayTo' => $data['fromMail'],
                'fromName' => $data['fromName'],
                'to' =>  $basicInfo->to_mail,
                'subject' => $data['subject'],
                'body' => $body,
            ];

            $mailService->sendMail($mailInfo);
        } catch (\Exception $e) {
            Log::error("MailToAdmin Job Error: " . $e->getMessage());
            return;
        }
        return;
    }
}
