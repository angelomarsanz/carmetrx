<?php

namespace Reda\Integraciones\Models\MercadoLibre;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminMeli extends Model
{
    // Definimos la tabla explícitamente
    protected $table = 'admins_melis';

    // Campos que permitimos llenar masivamente
    protected $fillable = [
        'admin_id',
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

    public function admin(): BelongsTo
    {
        // Relacionamos con la tabla 'admins' usando 'admin_id'
        return $this->belongsTo(\App\Models\Admin::class, 'admin_id');
    }

}