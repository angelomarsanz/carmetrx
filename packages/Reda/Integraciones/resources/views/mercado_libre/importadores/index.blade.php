@extends('integraciones::layout_bridge')

@section('contenido_del_plugin')
    <div id='indexImportadores'></div>
    <br /><br /><br />
    <div class="alert alert-info">
        Módulo: {{ config('integracion.module_version') }}
    </div>
    <p>Prueba de integración con Mercado Libre para importar productos.</p>
@endsection