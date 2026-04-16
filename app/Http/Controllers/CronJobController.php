<?php

namespace App\Http\Controllers;

use App\Http\Helpers\UserPermissionHelper;
use App\Jobs\IyzicoPendingMembership;
use App\Jobs\SubscriptionExpiredMail;
use App\Jobs\SubscriptionReminderMail;
use App\Models\BasicExtended;
use App\Models\BasicSetting;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CronJobController extends Controller
{
    public function expired()
    {
        $bs = BasicSetting::first();
        $be = BasicExtended::first();


        $exMembers = Membership::whereDate('expire_date', Carbon::now()->subDays(1))->get();
        foreach ($exMembers as $key => $exMember) {
            if (!empty($exMember->user)) {
                $user = $exMember->user;
                $currPackage = UserPermissionHelper::userPackage($user->id);

                if (is_null($currPackage)) {
                    SubscriptionExpiredMail::dispatch($user);
                }
            }
        }


        $rmdMembers = Membership::whereDate('expire_date', Carbon::now()->addDays($be->expiration_reminder))->get();
        foreach ($rmdMembers as $key => $rmdMember) {
            if (!empty($rmdMember->user)) {
                $user = $rmdMember->user;
                $nextPackageCount = Membership::query()->where([
                    ['user_id', $user->id],
                    ['start_date', '>', Carbon::now()->toDateString()]
                ])->where('status', '<>', 2)->count();

                if ($nextPackageCount == 0) {
                    SubscriptionReminderMail::dispatch($user,  $rmdMember->expire_date);
                }
            }
        }

        // Artisan::call("queue:work --stop-when-empty");
    }

    public function checkPayment()
    {
        try {
            //get iyzico pending memberships
            $pending_meberships = Membership::where([['payment_method', 'Iyzico'], ['status', 0]])->get();
            foreach ($pending_meberships as $pending_mebership) {
                IyzicoPendingMembership::dispatch($pending_mebership->id);
            }
        } catch (\Exception $e) {

            Log::error('Cron Job Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());  // Logs full stack trace
        }
    }
}
