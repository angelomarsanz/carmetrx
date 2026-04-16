<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonContentComplete extends Model
{
    use HasFactory;

    protected $table = 'user_lesson_content_complete';
    public $timestamps = false;

    protected $fillable = [
        'lesson_id',
        'user_id',
        'customer_id',
        'lesson_content_id',
        'type'
    ];

    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    public function customer() {
        return $this->belongsTo('App\Models\Customer');
    }

    public function lesson_content() {
        return $this->belongsTo('App\Models\User\Curriculum\LessonContent');
    }
}
