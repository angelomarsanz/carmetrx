<?php

namespace Reda\Integraciones\Models\MercadoLibre;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentMeli extends Model
{
    // Definimos la tabla explícitamente
    protected $table = 'agents_melis';

    // Campos que permitimos llenar masivamente
    protected $fillable = [
        'user_agent_id',
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

    public function agent(): BelongsTo
    {
        // Relacionamos con la tabla 'user_agents' usando 'user_agent_id'
        return $this->belongsTo(\App\Models\User\Agent\Agent::class, 'user_agent_id');
    }
}