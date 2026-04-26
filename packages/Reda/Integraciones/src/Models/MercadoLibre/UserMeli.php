<?php

namespace Reda\Integraciones\Models\MercadoLibre;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMeli extends Model
{
    // Definimos la tabla explícitamente
    protected $table = 'users_melis';

    // Campos que permitimos llenar masivamente
    protected $fillable = [
        'user_id',
        'datos_meli',
        'envios_meli',
        'respuesta_meli',
    ];

    // Esto convierte automáticamente el JSON de la BD en un Array de PHP
    protected $casts = [
        'datos_meli' => 'array',
        'envios_meli' => 'array',
        'respuesta_meli' => 'array',
    ];

    public function user(): BelongsTo
    {
        // Relacionamos con la tabla 'users' usando 'user_id'
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}