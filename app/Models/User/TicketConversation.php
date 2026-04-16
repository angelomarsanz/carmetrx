<?php

namespace App\Models\User;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketConversation extends Model
{
  use HasFactory;

  public $table = 'user_ticket_conversations';

  protected $guarded = [];

  public function user()
  {
    return $this->belongsTo(User::class, 'person_id', 'id');
  }

  public function customer()
  {
    return $this->belongsTo(Customer::class, 'person_id', 'id');
  }
  public function ticket()
  {
    return $this->belongsTo(SupportTicket::class, 'ticket_id', 'id');
  }
}
