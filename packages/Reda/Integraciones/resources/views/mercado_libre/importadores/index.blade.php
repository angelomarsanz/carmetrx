@extends('reda-integraciones::layout_bridge')

@section('contenido_del_plugin')
    <div id='indexImportadores'></div>
    <br /><br /><br />
    <div class="alert alert-info">
        Módulo: {{ config('reda-integraciones.module_version') }}
    </div>
    <p>Prueba de integración con Mercado Libre para importar productos.</p>
    <p>{{ __('Nombre del negocio') }}</p>
@endsection
