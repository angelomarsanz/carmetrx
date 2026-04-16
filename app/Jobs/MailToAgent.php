<?php

namespace App\Jobs;

use App\Models\User\Agent\Agent;
use App\Models\User\BasicSetting;
use App\Services\Mail\MailFromTenantWebsite;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MailToAgent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected  $agent, $mailData;
    /**
     * Create a new job instance.
     */
    public function __construct(Agent $agent, $mailData)
    {
        $this->agent = $agent;
        $this->mailData = $mailData;
    }

    /**
     * Execute the job.
     */
    public function handle(MailFromTenantWebsite $mailService): void
    {

        try {

            $tenantBs = $mailService->getTenantBasicInfo($this->agent->user_id);
            $name = $this->mailData['name'];
            $phone = $this->mailData['phone'];
            $email = $this->mailData['email'];
            $text = nl2br($this->mailData['message']);
            $toMail = $this->agent->email;
            $subject = 'Mail From Customer';
            $body = '<p>A new message has been sent.<br/>
        <strong>Client Name: </strong>' . $name . '<br/>
        <strong>Client Mail: </strong>' . $email . '<br/>
        <strong>Client Phone: </strong>' . $phone . '</p>
        <p><strong>Message :</strong> ' . $text . '</p> 
        <br><br>
        Best Regards,<br> ' . $tenantBs->website_title;
            $data = [
                'to' =>  $toMail,
                'subject' => $subject,
                'body' => $body,
            ];
            $mailService->sendMail($this->agent->user_id, $data);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
