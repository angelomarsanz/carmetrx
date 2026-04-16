<?php

namespace App\Models;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Models\User\Project\Wishlist as ProjectWishlist;
use App\Models\User\Property\Wishlist;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    public $table = "customers";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'user_id',
        'image',
        'username',
        'email',
        'email_verified_at',
        'password',
        'contact_number',
        'address',
        'city',
        'state',
        'country',
        'status',
        'verification_token',
        'reset_token',
        'edit_profile_status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function isActive()
    {
        return $this->status == 1;
    }

    public function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Constant::CUSTOMER_IMAGE . '/' . $value : 'assets/img/blank-user.jpg'
        );
    }

    public function scopeVerified($query)
    {
        return $query->whereNull('verification_token');
    }

    public function isVerified()
    {
        return $this->verification_token == null;
    }

    public function singup($tenantId, $request)
    {
        // generate a random string
        $randStr = str()->random(20);
        // generate a token
        $token = md5($randStr . $request->username . $request->email);

        $customer =  self::create([
            'username' => $request->username,
            'user_id' => $tenantId,
            'email' => $request->email_address,
            'password' => Hash::make($request->password),
            'verification_token' => $token,
            'status' => 0, // default deactive = 0
        ]);
        return $customer;
    }
    public function updateCustomer($request, $imageName = null)
    {
        $this->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'contact_number' => $request->phone_number,
            'image' => $imageName,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
        ]);
    }

    public static  function verifyEmail($token)
    {
        $customer = self::where('verification_token', $token)->firstOrFail();


        // after verify user email, put "null" in the "verification token"
        $customer->update([
            'email_verified_at' => now(),
            'status' => 1,
            'verification_token' => null
        ]);
        return $customer;
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $username = Customer::query()->where('email', request()->email)->pluck('username')->first();
        $subject = 'You are receiving this email because we received a password reset request for your account.';
        $body = "Recently you tried forget password for your account.Click below to reset your account password.
             <br>
             <a href='" . url('password/reset/' . $token . '/email/' . request()->email) . "'><button type='button' class='btn btn-primary'>Reset Password</button></a>
             <br>
             Thank you.
             ";
        $controller = new Controller();
        $controller->resetPasswordMail(request()->email, $username, $subject, $body);
        session()->flash('success', "we sent you an email. Please check your inbox");
    }

    public function customerDelete()
    {
        $customer = $this;
        $customer->propertyWishlists()->delete();
        $customer->projectWishlists()->delete();
        @unlink(public_path(Constant::CUSTOMER_IMAGE . '/' . $this->image));
        $customer->delete();
        return;
    }

    public function propertyWishlists()
    {
        return $this->hasMany(Wishlist::class, 'customer_id', 'id');
    }
    public function projectWishlists()
    {
        return $this->hasMany(ProjectWishlist::class, 'customer_id', 'id');
    }
}
