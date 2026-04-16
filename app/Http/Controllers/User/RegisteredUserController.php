<?php

namespace App\Http\Controllers\User;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
  // use TenantFrontendLanguage;
  public function index(Request $request)
  {
    $searchKey = null;

    if ($request->filled('info')) {
      $searchKey = $request['info'];
    }

    $users = Customer::where('user_id', Auth::guard('web')->user()->id)->when($searchKey, function ($query, $searchKey) {
      return $query->where('username', 'like', '%' . $searchKey . '%')
        ->orWhere('email', 'like', '%' . $searchKey . '%');
    })
      ->where('user_id', Auth::guard('web')->user()->id)
      ->orderBy('id', 'desc')
      ->paginate(10);

    return view('user.registered-users.index', compact('users'));
  }

  public function updateAccountStatus(Request $request, $id)
  {
    $user = Customer::where('id', $id)->where('user_id', Auth::guard('web')->user()->id)->firstOrFail();

    if ($request['account_status'] == 1) {
      $user->update(['status' => 1]);
    } else {
      $user->update(['status' => 0]);
    }

    session()->flash('success', __('Updated successfully!'));

    return redirect()->back();
  }

  public function show($id)
  {
    $userInfo = Customer::where('id', $id)->where('user_id', Auth::guard('web')->user()->id)->firstOrFail();
    $information['userInfo'] = $userInfo;
    return view('user.registered-users.details', $information);
  }

  public function changePassword($id)
  {
    $userInfo = Customer::where('id', $id)->where('user_id', Auth::guard('web')->user()->id)->firstOrFail();
    return view('user.registered-users.change-password', compact('userInfo'));
  }

  public function updatePassword(Request $request, $id)
  {
    $rules = [
      'new_password' => 'required|confirmed',
      'new_password_confirmation' => 'required'
    ];

    $messages = [
      'new_password.confirmed' => __('Password confirmation does not match'),
      'new_password_confirmation.required' => __('The confirm new password field is required')
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $user = Customer::where('id', $id)->where('user_id', Auth::guard('web')->user()->id)->firstOrFail();

    $user->update([
      'password' => Hash::make($request->new_password)
    ]);

    session()->flash('success', __('Updated successfully!'));
    return 'success';
  }

  public function destroy($id)
  {
    $this->delete($id);

    return redirect()->back()->with('success', __('Deleted successfully!'));
  }
  public function delete($id)
  {
    $user = Customer::findOrFail($id);
    $user->customerDelete();
    $user->delete();
    return;
  }


  public function bulkDestroy(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $this->delete($id);
    }

    session()->flash('success', __('Deleted successfully!'));

    return 'success';
  }


  public function emailStatus(Request $request)
  {
    $customer = Customer::where('id', $request->user_id)->where('user_id', Auth::guard('web')->user()->id)->firstOrFail();
    $customer->update([
      'email_verified_at' => $request->email_verified == 1 ? Carbon::now() : NULL,
      'verification_token' => $request->email_verified == 1 ? null : $customer->verification_token,
    ]);
    session()->flash('success', __('Updated successfully!'));
    return back();
  }

  public function secretLogin(Request $request)
  {
    $customer = Customer::where('id', $request->user_id)->first();
    if ($customer) {
      Auth::guard('customer')->login($customer);
      return redirect()->route('frontend.user.dashboard', $customer->user->username)
        ->withSuccess(__('You have successfully loggedin'));
    }

    return redirect("frontend.user.login")->withSuccess(__('Opps! You have entered invalid credentials'));
  }
}
