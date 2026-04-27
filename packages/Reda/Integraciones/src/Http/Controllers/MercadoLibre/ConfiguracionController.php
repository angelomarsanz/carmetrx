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
    public function verificarTokenMeli(Request $request, $datosUsuarioConectado = null)
    {
        if ($datosUsuarioConectado == null) {
            $datosUsuarioConectado = $request->input('datos_usuario_conectado', ''); 
        } 
    
        $respuesta = [
            'codigo_respuesta' => 0,
            'mensaje_respuesta' => __('Token encontrado exitosamente'),
            'token_meli' => 'xdffsfasdfasdf',
            'refresh_token_meli' => 'xdffsfasdfasdf'
        ];  
        return response()->json($respuesta);
    }
}
