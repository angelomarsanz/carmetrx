<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Mail\MailFromTenantWebsite;
use Illuminate\Foundation\Bus\Dispatchable;


class NotifyUserForProperty 
{
    use Dispatchable;
    protected $userId, $agent, $propertyTitle;
    /**
     * Create a new job instance.
     */
    public function __construct($userId, $agent, $propertyTitle)
    {
        $this->userId = $userId;
        $this->agent = $agent;
        $this->propertyTitle = $propertyTitle;
    }

    /**
     * Execute the job.
     */
    public function handle(MailFromTenantWebsite $mailService): void
    {

        $tenantBs = $mailService->getTenantBasicInfo($this->userId);
        $user = User::select('email')->find($this->userId);
        $data = [
            'to' =>  $user->email,
            'subject' => "New Property Listing Alert",
            'body' => "A new property listing has been posted by a agent. Please review the details at your earliest convenience.<br><br>

                    <strong>Agent Usename:</strong> " . $this->agent->username . " <br>
                    <strong>Property Title:</strong> " . $this->propertyTitle . "<br> 
                    <br><br>
                Best Regards,<br> " . $tenantBs->website_title,
        ];
        $mailService->sendMail($this->userId, $data);
    }
}
