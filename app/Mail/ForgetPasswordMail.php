<?php

namespace App\Mail;

use App\Services\Mail\MailFromTenantWebsite;
use Illuminate\Support\Facades\Log;

class ForgetPasswordMail
{

    public static function sendMail($recipient, $route)
    {

        $mailService = new MailFromTenantWebsite();
        try {
            $tenantId = $recipient->user_id;
            $template = $mailService->getMailTemplate($tenantId, 'reset_password');
            $tenantBasicInfo = $mailService->getTenantBasicInfo($tenantId);


            $relaceBodyData =  [
                'customer_name' =>  $recipient->username,
                'password_reset_link' => $route,
                'website_title' => $tenantBasicInfo->website_title,
            ];
            $mailBody = $mailService->prepareMailBody($template->mail_body, $relaceBodyData);

            $mailInfo = [
                'to' =>  $recipient->email,
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
