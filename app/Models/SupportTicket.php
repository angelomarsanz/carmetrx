<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function conversations()
    {
        return $this->hasMany(SupportTicketConversation::class, 'support_ticket_id', 'id');
    }

    public function  deleteTicket()
    {

        $ticket = $this;

        // delete ticket conversations
        $conversations = $ticket->conversations()->get();

        if (count($conversations) > 0) {
            foreach ($conversations as $conversation) {
                @unlink(public_path('assets/file/ticket-files/') . $conversation->attachment);

                $conversation->delete();
            }
        }

        // delete ticket
        @unlink(public_path('assets/file/ticket-files/') . $ticket->attachment);

        $ticket->delete();
    }
}
