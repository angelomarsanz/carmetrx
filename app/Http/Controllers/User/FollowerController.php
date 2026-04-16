<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\Follower;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class FollowerController extends Controller
{

    public function follow($id)
    {
        $followCheck = Follower::query()->where([
            ['follower_id', Auth::guard('web')->user()->id],
            ['following_id', $id],
        ])->first();
        if (is_null($followCheck)) {
            $follow = new Follower();
            $follow->follower_id = Auth::guard('web')->user()->id;
            $follow->following_id = $id;
            $follow->save();
            Session::flash('success', __('You have followed successfully!'));
        }
        return redirect()->back();
    }

    public function follower()
    {
        $data['users'] = [];
        $followerListIds = Follower::query()->where('following_id', Auth::guard('web')->user()->id)->pluck('follower_id');
        if (count($followerListIds) > 0) {
            $data['users'] = User::whereIn('id', $followerListIds)->paginate(10);
        }
        return view('user.follower.index', $data);
    }

    public function following()
    {
        $data['users'] = [];
        $followingListIds = Follower::query()->where('follower_id', Auth::guard('web')->user()->id)->pluck('following_id');
        if (count($followingListIds) > 0) {
            $data['users'] = User::whereIn('id', $followingListIds)->paginate(10);
        }
        return view('user.following.index', $data);
    }

    public function unfollow($id)
    {
        $followCheck = Follower::query()
            ->where([
                ['follower_id', Auth::guard('web')->user()->id],
                ['following_id', $id],
            ])->first();
        if (!is_null($followCheck)) {
            $followCheck->delete();
            Session::flash('success', __('You have unfollowed successfully!'));
            return redirect()->back();
        } else {
            Session::flash('warning', __('You cannot unfollow the user!'));
            return redirect()->back();
        }
    }
}
