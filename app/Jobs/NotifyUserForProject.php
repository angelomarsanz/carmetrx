<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\User\BasicSetting;
use App\Services\Mail\MailFromTenantWebsite;
use Illuminate\Foundation\Bus\Dispatchable;

class NotifyUserForProject
{
    use Dispatchable;
    protected $userId, $agent, $projectTitle;


    /**
     * Create a new job instance.
     */
    public function __construct($userId, $agent, $projectTitle)
    {
        $this->userId = $userId;
        $this->agent = $agent;
        $this->projectTitle = $projectTitle;
    }

    /**
     * Execute the job.
     */
    public function handle(MailFromTenantWebsite $mailService): void
    {
        $tenantBs = BasicSetting::where('user_id', $this->userId)->select('website_title')->first();
        $user = User::select('email')->find($this->userId);
        $data = [
            'to' =>  $user->email,
            'subject' => "New Project Listing Alert",
            'body' => "A new project listing has been posted by a agent. Please review the details at your earliest convenience.<br><br>

                    <strong>Agent Usename:</strong> " . $this->agent->username . " <br>
                    <strong>Property Title:</strong> " . $this->projectTitle . "<br> 
                    <br><br>
                Best Regards,<br> " . $tenantBs->website_title,
        ];

        $mailService->sendMail($this->userId, $data);
    }
}
