<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Mail\MailFromSuperAdmin;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class UserResetPasswordMail
{
    use Dispatchable;

    protected $tenant;
    protected $token;

    /**
     * Create a new job instance.
     *
     * @param Model -  \App\Models\User or \App\Models\User\Agent  $recipient  // Assuming both Agent and User  
     * @param  string  $token   
     *  
     */
    public function __construct(User $tenant, $token)
    {
        $this->tenant = $tenant;
        $this->token = $token;
    }

    /**
     * Execute the job.
     */
    public function handle(MailFromSuperAdmin $mailService): void
    {
        try {
            $basicInfo = $mailService->getAdmintBasicInfo();
            $rotue = route('user.reset.password.form', ['token' => $this->token, 'email' => $this->tenant->email]);
            $subject = 'Password Reset (' . $basicInfo->website_title . ')';
            $mailBody = "Please click the below link to reset your password.
            <br>
             <br>
             <a href='" . $rotue . "'>" . $rotue . "</a>
             <br>
             <br>
             Thank you.
             ";


            $mailInfo = [
                'to' =>  $this->tenant->email,
                'subject' => $subject,
                'body' => $mailBody,
            ];

            $mailService->sendMail($mailInfo);
        } catch (\Exception $e) {
            Log::error("UserRtesetPasswordMail Job Error: " . $e->getMessage());
            return;
        }
        return;
    }
}
