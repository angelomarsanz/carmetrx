@php
    /**
     * Lógica de detección por Guards (basada en tu plugin de garantías)
     * Esto asegura que el layout corresponda al nivel de acceso del usuario.
     */
    if (auth()->guard('admin')->check()) {
        $layout = 'admin.layout';
    } elseif (auth()->guard('agent')->check()) {
        $layout = 'agent.layout';
    } else {
        // Por defecto para usuarios/clientes o si no hay un guard específico detectado
        $layout = 'user.layout';
    }
@endphp

{{-- Extendemos el layout detectado dinámicamente --}}
@extends($layout)

{{-- 
    IMPORTANTE: Ya no inyectamos CSS/JS aquí porque ya los pusimos 
    en admin/layout, user/layout y agent/layout de forma global.
--}}

@section('content')
    {{-- 
       Encapsulamos el contenido de las vistas del plugin.
       Mantenemos el nombre 'contenido_del_plugin' para que todas 
       tus vistas actuales sigan funcionando sin cambios.
    --}}
    @yield('contenido_del_plugin')
@endsection

{{-- 
    Mantenemos estas secciones por si alguna vista específica 
    necesita inyectar algo extra (como variables JS o estilos locales)
--}}
@section('scripts')
    @yield('extra_plugin_scripts')
@endsection

@section('styles')
    @yield('extra_plugin_styles')
@endsection