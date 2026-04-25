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
    public function verificarTokenMeli(Request $request)
    {
        $respuesta = [
            'codigo_respuesta' => 0,
            'usuario_duenio_token' => 'Usuario Ejemplo',
            'mensaje_respuesta' => 'Token encontrado exitosamente',
            'token_meli' => 'xdffsfasdfasdf',
            'refresh_token_meli' => 'xdffsfasdfasdf'
        ];  
        return response()->json($respuesta);
    }
}
