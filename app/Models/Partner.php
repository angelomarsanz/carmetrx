<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    // public $timestamps = false;
    public $fillable = [
        'language_id',
        'image',
        'url',
        'serial_number',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public static function storePartner($request, $imageName)
    {
        return self::create([
            'language_id' => $request['language'],
            'image' => $imageName,
            'url' => $request['url'],
            'serial_number' => $request['serial_number'],
        ]);
    }

    public  function updatePartner($request, $imageName)
    {
        return $this->update([
            'image' => $imageName,
            'url' => $request['url'],
            'serial_number' => $request['serial_number'],
        ]);
    }
}
