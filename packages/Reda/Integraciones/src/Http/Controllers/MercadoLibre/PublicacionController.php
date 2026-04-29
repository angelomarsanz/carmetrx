<?php
namespace Reda\Integraciones\Http\Controllers\MercadoLibre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Reda\Integraciones\Traits\MercadoLibre\MeliRequestsTrait;

class PublicacionController extends Controller
{
    use MeliRequestsTrait;

    public function index()
    {
        //
    }
    public function replicarPublicacionMeli()
    {
        $resultado = $this->enviar_solicitud_meli('items', 'POST', $datosVehiculo, true, $token);

        if (!$resultado['success']) {
            $mensajeUsuario = "Hubo un problema con la publicación: " . $resultado['mensaje_respuesta'];

            if (!empty($resultado['causas'])) {
                $mensajeUsuario .= "<ul>";
                foreach ($resultado['causas'] as $errorEspecifico) {
                    $mensajeUsuario .= "<li>" . e($errorEspecifico) . "</li>";
                }
                $mensajeUsuario .= "</ul>";
            }

            // Retornar a la vista con el error formateado
            return back()->with('error_publicacion', $mensajeUsuario);
        }    //
    }
}
