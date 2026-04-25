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
        $datos_usuario_conectado = $request->input('datos_usuario_conectado', ''); 
    
            // Aquí iría la lógica real para verificar el token con MercadoLibre usando el prefijo
            // Por ahora, devolvemos una respuesta simulada para propósitos de prueba
        $respuesta = [
            'codigo_respuesta' => 0,
            'mensaje_respuesta' => __('Token encontrado exitosamente'),
            'token_meli' => 'xdffsfasdfasdf',
            'refresh_token_meli' => 'xdffsfasdfasdf'
        ];  
        return response()->json($respuesta);
    }
}
