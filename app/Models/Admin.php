<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
  use Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'role_id', 'username', 'email', 'password', 'first_name', 'last_name', 'image', 'status'
  ];


  public function role()
  {
    return $this->belongsTo('App\Models\Role');
  }

  public function message()
  {
    return $this->hasMany(ServiceOrderMessage::class, 'person_id', 'id');
  }

  public function ticket()
  {
    return $this->hasMany(SupportTicket::class, 'admin_id', 'id');
  }

  public function ticketConversation()
  {
    return $this->hasMany(TicketConversation::class, 'person_id', 'id');
  }
}
