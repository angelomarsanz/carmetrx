@extends('reda-integraciones::layout_bridge')

@section('contenido_del_plugin')
    <input type="hidden" name="codigo_temporal" id="codigo_temporal" value=<?php echo isset($_GET['code']) ? $_GET['code'] : "error"; ?> />
    <h3>{{ __('Configuración de la conexión con Mercado Libre') }}</h3>
    <div id='indexConfiguracionesMercadoLibre'></div>
@endsection