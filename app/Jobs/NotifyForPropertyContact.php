<?php

namespace App\Jobs;

use App\Models\User\BasicSetting;
use App\Services\Mail\MailFromTenantWebsite;
use Exception;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class NotifyForPropertyContact
{
    use Dispatchable;

    public $userId, $request;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $request)
    {
        $this->userId = $userId;
        $this->request = $request;
    }

    /**
     * Execute the job.
     */
    public function handle(MailFromTenantWebsite $mailService): void
    {
        try {

            $tenantBs = BasicSetting::where('user_id', $this->userId)->select('website_title')->first();
            $name = $this->request['name'];
            $phone = $this->request['phone'];
            $email = $this->request['email'];
            $text = $this->request['message'];
            $toMail = $this->request['to_mail'];
            $subject = 'New Message Regarding Your Property Listing';
            $body = '<h3>The message is receive from</h3> 
        <p><strong>Enquirer Name: </strong>' . $name . '<br/>
        <strong>Enquirer Mail: </strong>' . $email . '<br/>
        <strong>Enquirer Phone: </strong>' . $phone . '</p>
        <p><strong>Message: </strong> ' . nl2br($text) . '</p> 
        <br><br>
        Best Regards,<br> ' . $tenantBs->website_title;
            $data = [
                'to' =>  $toMail,
                'subject' => $subject,
                'body' => $body,
            ];
            $mailService->sendMail($this->userId, $data);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
