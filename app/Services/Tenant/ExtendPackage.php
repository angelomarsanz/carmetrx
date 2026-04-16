<?php

namespace App\Services\Tenant;

use App\Models\User;
use App\Models\Package;
use App\Models\Membership;
use App\Jobs\TenantPackageExtendMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use App\Models\BasicSetting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class ExtendPackage
{
  public function exdendPackage($data)
  {

    try {
      DB::beginTransaction();
      // Retrieve the user by ID
      $tenant = User::findOrFail($data['user_id']);

      // Check for an active membership
      $previousMembership = $this->getActiveMembership($tenant->id);

      // check previous membership;
      if ($previousMembership) {
        $this->handlePreviousMembership($previousMembership, $data['transaction_details'], $data['start_date']);
      }

      // Create a new membership for the user
      $membership = $this->createMembership($tenant->id, $data);

      // Create a invoice
      $invoice =  $this->makeInvoice($tenant, $membership, $data['package_title'], $data['website_title']);

      // Send mail with invoice
      if ($membership->payment_method != 'Iyzico') {
        $this->sendMailWithInvoice($tenant, $data, $invoice);
      }

      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      Log::error('Package extended failed', ['error' => $e->getMessage()]);
      throw new Exception('Failed to exdended package.', 0, $e);
    }
  }


  /**
   * Retrieve the active membership for a tenant.
   */
  private function getActiveMembership($tenantId)
  {
    return Membership::query()
      ->select('id', 'package_id', 'is_trial')
      ->where([
        ['user_id', $tenantId],
        ['start_date', '<=', Carbon::now()->toDateString()],
        ['expire_date', '>=', Carbon::now()->toDateString()],
      ])
      ->where('status', 1)
      ->orderBy('created_at', 'DESC')
      ->first();
  }


  /**
   * Handle any logic for a tenant's previous membership.
   */
  private function handlePreviousMembership($previousMembership, $transactionDetails, $newStartDate)
  {
    $previousPackage = Package::query()
      ->select('term')
      ->where('id', $previousMembership->package_id)
      ->first();

    if ($previousPackage && ($previousPackage->term === 'lifetime' || $previousMembership->is_trial == 1) && $transactionDetails !== 'offline') {
      $membership = Membership::find($previousMembership->id);
      $membership->expire_date = $newStartDate;
      $membership->save();
    }
  }


  /**
   * Create a new membership record.
   */
  private function createMembership($tenantId, $data)
  {
    $membership = new Membership();
    return $membership->register($tenantId, $data);
  }
  /**
   * Generate a PDF invoice for the membership.
   * @param User $tenant
   * @param Membership $membership
   * @param  $packageTitle
   * @param $websiteTitle
   */

  public function makeInvoice($tenant, $membership, $packageTitle, $websiteTitle)
  {
    if ($membership->payment_method == 'Iyzico') {
      return null;
    }
    $fileName = uniqid('TMI') . ".pdf";
    $invoiceDir = public_path('assets/front/invoices/');
    $pathName = $invoiceDir . $fileName;

    @mkdir($invoiceDir, 0775, true);

    $bs = BasicSetting::select('logo')->first();
    $logoPath = public_path('assets/front/img/' . ($bs->logo ?? 'noimage.jpg'));
    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));

    // Render Blade view to HTML
    $html = view('pdf.membership', compact('tenant', 'membership', 'packageTitle', 'websiteTitle', 'logoBase64'))->render();

    // Initialize mPDF with RTL and UTF-8 support
    $mpdf = new Mpdf([
      'mode' => 'utf-8',
      'format' => 'A4',
      'default_font' => 'dejavusans',
      'autoScriptToLang' => true,
      'autoLangToFont' => true,
    ]);

    // Write HTML content to PDF
    $mpdf->WriteHTML($html);

    // Save PDF file
    $mpdf->Output($pathName, \Mpdf\Output\Destination::FILE);

    return $pathName;
  }


  /**
   * Send email to the tenant.
   *
   * @param User $tenant
   * @param $data, 
   * @param $invoice
   */
  protected function sendMailWithInvoice(User $tenant, $data, $invoice)
  {
    $packageTitle = $data['package_title'];
    $packagePrice = $data['package_price'];
    $activation_date = $data['start_date'];
    $expire_date = Carbon::parse($data['expire_date'])->format('Y') == '9999' ? 'Lifetime' : $data['expire_date'];

    dispatch_sync(new TenantPackageExtendMail($tenant, $packageTitle, $packagePrice, $activation_date, $expire_date, $invoice));
  }
}
