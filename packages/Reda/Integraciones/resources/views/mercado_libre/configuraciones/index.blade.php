@extends('reda-integraciones::layout_bridge')

@section('contenido_del_plugin')
    <div id='indexConfiguracionesMercadoLibre'></div>
    <br /><br /><br />
    <div class="alert alert-info">
        Módulo: {{ config('reda-integraciones.module_version') }}
    </div>
    <p>{{ __('Configuración conexión con Mercado Libre') }}</p>
@endsection
