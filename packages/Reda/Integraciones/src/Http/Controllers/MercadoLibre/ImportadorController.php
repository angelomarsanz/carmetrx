<?php
namespace Reda\Integraciones\Http\Controllers\MercadoLibre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImportadorController extends Controller
{
    public function index()
    {
        return view('integraciones::mercado_libre.importadores.index');
    }
}