<?php
namespace Reda\Integraciones\Http\Controllers\MercadoLibre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        return view('reda-integraciones::mercado_libre.configuraciones.index');
    }
}
