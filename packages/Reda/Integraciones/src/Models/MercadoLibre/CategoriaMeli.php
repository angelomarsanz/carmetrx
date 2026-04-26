<?php

namespace Reda\Integraciones\Models\MercadoLibre;

use Illuminate\Database\Eloquent\Model;

class CategoriaMeli extends Model
{
    // Definimos la tabla explícitamente
    protected $table = 'categorias_melis';

    // Campos que permitimos llenar masivamente
    protected $fillable = [
        'id_categoria_meli',
        'nombre_categoria_meli',
        'atributos_generales',
        'atributos_especificos',
    ];

    // Esto convierte automáticamente el JSON de la BD en un Array de PHP
    protected $casts = [
        'atributos_generales' => 'array',
        'atributos_especificos' => 'array',
    ];
}