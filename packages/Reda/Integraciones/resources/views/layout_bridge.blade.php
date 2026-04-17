{{-- Extiende el layout del Dashboard de Usuario --}}
@extends('user.layout')

@section('styles')
    {{-- Inyectamos los estilos del plugin --}}
    <link rel="stylesheet" href="{{ asset('css/integraciones.css?v=' . time()) }}">
    
    {{-- Permitimos que las vistas inyecten más estilos si lo necesitan --}}
    @yield('extra_plugin_styles')
@endsection

@section('content')
    {{-- 
       El layout de usuario usa @yield('content') dentro de un div .page-inner.
       Aquí ponemos el yield de tu plugin.
    --}}
    @yield('contenido_del_plugin')
@endsection

{{-- 
    Como el layout de usuario no tiene un @yield('scripts'), 
    usamos la sección 'scripts' que suele estar definida en el partial 
    o simplemente la definimos para que Laravel la procese.
--}}
@section('scripts')
    <script src="{{ asset('js/integraciones.js?v=' . time()) }}"></script>
    @yield('extra_plugin_scripts')
@endsection