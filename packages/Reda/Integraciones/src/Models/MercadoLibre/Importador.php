<?php

namespace Reda\Integraciones\Models\MercadoLibre;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Importador extends Model
{
    // Definimos la tabla explícitamente
    protected $table = 'importadores';

    // Campos que permitimos llenar masivamente
    protected $fillable = [
        'user_id',
        'nombre_concesionario',
        'identificador_meli',
        'paso_ejecutado'
    ];

    /**
     * Relación con el Usuario (Inmobiliaria)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}