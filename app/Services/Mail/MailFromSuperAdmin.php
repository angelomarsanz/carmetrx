<?php

namespace App\Services\Mail;

use App\Models\BasicExtended;
use App\Models\BasicSetting;
use App\Models\EmailTemplate;
use Exception;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailFromSuperAdmin
{

  /**
   * This service work for send mail from tenant website 
   * Here is useable method for this service
   * 
   * @method getMailTemplate( string $emailType)
   * @method prepareMailBody($body, $replaceData)
   * @method sendMail($data)
   * @method getAdmintBasicInfo()
   */

  protected $adminBasicExtended;
  protected $adminBasicSettings;
  protected $adminMailTemplate;




  public function __construct()
  {
    $this->adminBasicExtended = new BasicExtended();
    $this->adminMailTemplate  = new EmailTemplate();
    $this->adminBasicSettings  = new BasicSetting();
  }

  public function getMailTemplate(string $emailType)
  {
    $mailTemplate = $this->adminMailTemplate->where('email_type', $emailType)->first();
    return $mailTemplate;
  }

  public function prepareMailBody($body, $replaceData)
  {

    if (array_key_exists('username', $replaceData)) {
      $body = preg_replace("/{username}/", $replaceData['username'], $body);
    }
    if (array_key_exists('replaced_package', $replaceData)) {
      $body = preg_replace("/{replaced_package}/", $replaceData['replaced_package'], $body);
    }
    if (array_key_exists('removed_package_title', $replaceData)) {
      $body = preg_replace("/{removed_package_title}/", $replaceData['removed_package_title'], $body);
    }
    if (array_key_exists('package_title', $replaceData)) {
      $body = preg_replace("/{package_title}/", $replaceData['package_title'], $body);
    }
    if (array_key_exists('package_price', $replaceData)) {
      $body = preg_replace("/{package_price}/", $replaceData['package_price'], $body);
    }
    if (array_key_exists('discount', $replaceData)) {
      $body = preg_replace("/{discount}/", $replaceData['discount'], $body);
    } else {
      $body = preg_replace("/{discount}/", '', $body);
    }
    if (array_key_exists('total', $replaceData)) {
      $body = preg_replace("/{total}/", $replaceData['total'], $body);
    } else {
      $body = preg_replace("/{total}/", '', $body);
    }
    if (array_key_exists('activation_date', $replaceData)) {
      $body = preg_replace("/{activation_date}/", $replaceData['activation_date'], $body);
    }
    if (array_key_exists('expire_date', $replaceData)) {
      $body = preg_replace("/{expire_date}/", $replaceData['expire_date'], $body);
    }
    if (array_key_exists('requested_domain', $replaceData)) {
      $body = preg_replace("/{requested_domain}/", "<a href='http://" . $replaceData['requested_domain'] . "'>" . $replaceData['requested_domain'] . "</a>", $body);
    }
    if (array_key_exists('previous_domain', $replaceData)) {
      $body = preg_replace("/{previous_domain}/", "<a href='http://" . $replaceData['previous_domain'] . "'>" . $replaceData['previous_domain'] . "</a>", $body);
    }
    if (array_key_exists('current_domain', $replaceData)) {
      $body = preg_replace("/{current_domain}/", "<a href='http://" . $replaceData['current_domain'] . "'>" . $replaceData['current_domain'] . "</a>", $body);
    }
    if (array_key_exists('subdomain', $replaceData)) {
      $body = preg_replace("/{subdomain}/", "<a href='http://" . $replaceData['subdomain'] . "'>" . $replaceData['subdomain'] . "</a>", $body);
    }
    if (array_key_exists('last_day_of_membership', $replaceData)) {
      $body = preg_replace("/{last_day_of_membership}/", $replaceData['last_day_of_membership'], $body);
    }
    if (array_key_exists('login_link', $replaceData)) {
      $body = preg_replace("/{login_link}/", $replaceData['login_link'], $body);
    }
    if (array_key_exists('customer_name', $replaceData)) {
      $body = preg_replace("/{customer_name}/", $replaceData['customer_name'], $body);
    }
    if (array_key_exists('verification_link', $replaceData)) {
      $body = preg_replace("/{verification_link}/", $replaceData['verification_link'], $body);
    }
    if (array_key_exists('website_title', $replaceData)) {
      $body = preg_replace("/{website_title}/", $replaceData['website_title'], $body);
    }

    return $body;
  }

  public function sendMail($data)
  {
    try {
      $smtpInfo = $this->getSmtpInfo();
      // $adminInfo = $this->getAdmintBasicInfo();

      if ($smtpInfo->is_smtp) {
        $this->configureSmtp($smtpInfo);
      }

      $this->dispatchMail($data, $smtpInfo);

      if (array_key_exists('sessionMessage', $data)) {
        return ['success' => $data['sessionMessage']];
      }
    } catch (Exception $e) {
      Log::error('Mail Config error: ' . $e->getMessage());
    }
  }

  protected function getSmtpInfo()
  {
    return $this->adminBasicExtended->select('is_smtp', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')->first();
  }
  public function getAdmintBasicExtend()
  {
    return $this->adminBasicExtended->select('to_mail')->first();
  }
  public function getAdmintBasicInfo()
  {
    return  $this->adminBasicSettings->select('website_title')->first();
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

  protected function dispatchMail($data, $smtpInfo)
  {
    try {
      $filePath = isset($data['invoice']) ? $data['invoice'] : null;

      Mail::send([], [], function (Message $message) use ($data, $smtpInfo, $filePath) {
        $fromMail = $smtpInfo->from_mail;
        $replyTo = $data['replayTo'] ?? $smtpInfo->from_mail;
        $fromName = $data['fromName'] ?? $smtpInfo->from_name;
        $message->to($data['to'])
          ->replyTo($replyTo)
          ->subject($data['subject'])
          ->from($fromMail, $fromName)
          ->html($data['body'], 'text/html');

        if ($filePath && file_exists($filePath)) {
          $message->attach($filePath, [
            'as' => 'Invoice.pdf',
            'mime' => 'application/pdf',
          ]);
        }
      });
    } catch (Exception $e) {
      Log::error('Mail send error: ' . $e->getMessage());
    } finally {
      // Delete the invoice only if it exists and was sent
      if ($filePath && file_exists($filePath)) {
        @unlink($filePath);
      }
    }
  }
}
