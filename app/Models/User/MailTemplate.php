<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailTemplate extends Model
{
    use HasFactory;

    protected $table = 'user_mail_templates';
    public $guarded = [];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store($tenantId, $data)
    {
        return self::create([
            'user_id' => $tenantId,
            'mail_type' => $data['mail_type'],
            'mail_subject' => $data['mail_subject'],
            'mail_body' => $data['mail_body'],
        ]);
    }
    
}
