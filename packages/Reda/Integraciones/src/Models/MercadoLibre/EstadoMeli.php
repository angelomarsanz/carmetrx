<?php

namespace Reda\Integraciones\Models\MercadoLibre;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstadoMeli extends Model
{
    // Definimos la tabla explícitamente
    protected $table = 'estados_melis';

    // Campos que permitimos llenar masivamente
    protected $fillable = [
        'user_state_id',
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

    public function state(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\Property\State::class, 'user_state_id');
    }
}
