<?php

namespace App\Services\Mail;

use App\Models\BasicExtended;
use App\Models\User\BasicSetting;
use App\Models\User\MailTemplate;
use Exception;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailFromTenantWebsite
{

  /**
   * This service work for send mail from tenant website 
   * Here is useable method for this service
   * 
   * @method getMailTemplate(int $tenantId, string $emailType)
   * @method prepareMailBody($body, $replaceData)
   * @method sendMail($tenantId, $data)
   * @method getTenantBasicInfo($tenantId)
   */

  // protected $adminBasicExtended;
  // protected $tenantBasicSetting;
  // protected $tenantMailTemplate;




  // public function __construct()
  // {
  //   $this->adminBasicExtended = new BasicExtended();
  //   $this->tenantBasicSetting = new BasicSetting();
  //   $this->tenantMailTemplate = new MailTemplate();
  // }
  // protected function setMailTemplate()
  // {
  //   $this->tenantMailTemplate = new MailTemplate();
  // }

  public function getMailTemplate(int $tenantId, string $emailType)
  {
    // if (!$this->tenantMailTemplate) {
    //   $this->setMailTemplate();
    // }

    $mailTemplate = MailTemplate::where([['user_id', $tenantId], ['mail_type', $emailType]])->first();
    return $mailTemplate;
  }

  public function prepareMailBody($body, $replaceData)
  {
    if (array_key_exists('username', $replaceData)) {
      $body = preg_replace("/{username}/", $replaceData['username'], $body);
    }
    if (array_key_exists('customer_name', $replaceData)) {
      $body = preg_replace("/{customer_name}/", $replaceData['customer_name'], $body);
    }
    if (array_key_exists('verification_link', $replaceData)) {
      $body = preg_replace("/{verification_link}/", $replaceData['verification_link'], $body);
    }
    if (array_key_exists('password_reset_link', $replaceData)) {
      $body = preg_replace("/{password_reset_link}/", $replaceData['password_reset_link'], $body);
    }
    if (array_key_exists('website_title', $replaceData)) {
      $body = preg_replace("/{website_title}/", $replaceData['website_title'], $body);
    }

    if (array_key_exists('login_url', $replaceData)) {
      $body = preg_replace("/{login_url}/", $replaceData['login_url'], $body);
    }
    if (array_key_exists('password', $replaceData)) {
      $body = preg_replace("/{password}/", $replaceData['password'], $body);
    }

    return $body;
  }

  public function sendMail($tenantId, $data)
  {
    try {
      $smtpInfo = $this->getSmtpInfo();
      $tenantInfo = $this->getTenantBasicInfo($tenantId);

      if ($smtpInfo->is_smtp) {
        $this->configureSmtp($smtpInfo);
      }

      $this->dispatchMail($data, $tenantInfo, $smtpInfo);

      if (array_key_exists('sessionMessage', $data)) {
        return ['success' => $data['sessionMessage']];
      }
    } catch (Exception $e) {
      Log::error('Mail Config error: ' . $e->getMessage());
    }
  }

  protected function getSmtpInfo()
  {
    return BasicExtended::select('is_smtp', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')->first();
  }

  public function getTenantBasicInfo($tenantId)
  {
    return BasicSetting::where('user_id', $tenantId)->select('from_name', 'website_title', 'email')->first();
  }

  protected function configureSmtp($smtpInfo)
  {
    $smtp = [
      'transport' => 'smtp',
      'host' => $smtpInfo->smtp_host,
      'port' => $smtpInfo->smtp_port,
      'encryption' => $smtpInfo->encryption,
      'username' => $smtpInfo->smtp_username,
      'password' => $smtpInfo->smtp_password,
      'timeout' => null,
      'auth_mode' => null,
    ];

    Config::set('mail.mailers.smtp', $smtp);
  }

  protected function dispatchMail($data, $tenantInfo, $smtpInfo)
  {

    try {
      Mail::send([], [], function (Message $message) use ($data, $tenantInfo, $smtpInfo) {
        $fromMail = $smtpInfo->from_mail;
        $replyTo = $tenantInfo->email;
        $fromName = $tenantInfo->from_name;
        $message->to($data['to'])
          ->replyTo($replyTo)
          ->subject($data['subject'])
          ->from($fromMail, $fromName)
          ->html($data['body'], 'text/html');

        if (array_key_exists('invoice', $data)) {
          $message->attach($data['invoice'], [
            'as' => 'Invoice',
            'mime' => 'application/pdf',
          ]);
        }
      });
    } catch (Exception $e) {
      Log::error('Mail send error: ' . $e->getMessage());
    }
  }
}
