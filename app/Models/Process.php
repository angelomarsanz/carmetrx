<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    use HasFactory;
    public $timestamps = false;

    public $fillable = [
        'language_id',
        'icon',
        'title',
        'text',
        'serial_number'
    ];

    public function language()
    {
        return $this->belongsTo('App\Models\Language');
    }
    public static function storeProcess($request)
    {
        return self::create([
            'icon' => $request->icon,
            'language_id' => $request->language,
            'title' => $request->title,
            'text' => $request->text,
            'serial_number' => $request->serial_number,
        ]);
    }
    public  function updateProcess($request)
    {
        return  $this->update([
            'icon' => $request->icon,
            'title' => $request->title,
            'text' => $request->text,
            'serial_number' => $request->serial_number,
        ]);
    }
}
