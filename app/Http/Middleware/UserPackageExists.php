<?php

namespace App\Http\Middleware;

use App\Http\Helpers\UserPermissionHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserPackageExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('web')->user()) {
            $currnetPackage = UserPermissionHelper::currPackageOrPending(Auth::guard('web')->user()->id);
            $nextPackage = UserPermissionHelper::nextPackage(Auth::guard('web')->user()->id);

            if (is_null($currnetPackage)) {
                if (is_null($nextPackage)) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'status' => 'error',
                            'deactive' => 'Your membership is expired. Please purchase a new package / extend the current package.',
                        ], 403);
                    } else {
                        session()->flash('warning', 'Your membership is expired. Please purchase a new package / extend the current package.');
                        return redirect()->route('vendor.dashboard');
                    }
                }
            }
        } elseif (Auth::guard('agent')->user()) {
            if (Auth::guard('agent')->user()->vendor_id == 0) {
                return $next($request);
            } else {
                $currnetPackage = UserPermissionHelper::currPackageOrPending(Auth::guard('agent')->user()->vendor_id);
                $nextPackage = UserPermissionHelper::nextPackage(Auth::guard('agent')->user()->vendor_id);
                if (is_null($currnetPackage)) {
                    if (is_null($nextPackage)) {
                        if ($request->expectsJson()) {
                            return response()->json([
                                'status' => 'error',
                                'deactive' => 'Your membership is expired. Please purchase a new package / extend the current package.',
                            ], 403);
                        } else {
                            session()->flash('warning', 'Your membership is expired. Please purchase a new package / extend the current package.');
                            return redirect()->route('agent.dashboard');
                        }
                    }
                }
            }
        }
        return $next($request);
    }
}
