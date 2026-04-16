<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketConversation extends Model
{
  use HasFactory;

  public function ticket()
  {
    return $this->belongsTo(SupportTicket::class, 'support_ticket_id', 'id');
  }

  public function user()
  {
    return $this->belongsTo(User::class, 'person_id', 'id');
  }

  public function admin()
  {
    return $this->belongsTo(Admin::class, 'person_id', 'id');
  }
}
