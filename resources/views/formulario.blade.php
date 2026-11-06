@extends('layouts.app')

@section('titulo', $errors->any() ? 'Error en el formulario - Generador de código QR' : 'Generador de código QR')

@section('contenido')

{{--Estilos repetidos guardados en variables--}}
@php
    $estiloSeccion = 'background-color:#29547e; padding:clamp(20px, 5vw, 25px); margin:0 0 25px; border-radius: 10px; box-shadow:0 2px 8px rgba(0,0,0,0.1);';
    $estiloH2      = 'margin:0 0 20px; text-align: center; font-size: 1.5rem;';
    $estiloCampo   = 'margin:0 0 18px;';
    $estiloLabel   = 'display: block; margin 0 0 6px; font-weight: bold;';
    //box-sizing para que width esta 100% + padding no salga de la sección
    $estiloInput   = 'box-sizing: border-box; width: 100%; margin: 0; padding: 10px; font-size: 1rem; border-radius: 5px;';
    $bordeNormal   = 'border: 1px solid #999;';
    //Errores
    $bordeError    = 'border: 3px solid #ffb3c1;';
    $estiloError   = 'margin: 6px 0 0; color: #ffb3c1; font-weight: bold;';
    $estiloAsterisco = 'color: #ffb3c1;';
    //Oculto a la vista, info para lectores de la pantalla
    $estiloOculto  = 'position: absolute; width: 1px; height: 1px; padding:0; margin: -1px; overflow: hidden; clip:rect(0,0,0,0); white-space: nowrap; border:0;';
@endphp

{{-- ERRORES --}}
@if ($errors->any())
    <section id="resumen-errores" tabindex="-1" aria-labelledby="titulo-errores" style="{{ $estiloSeccion }} border: 3px solid #ffb3c1;">
        <h2 id="titulo-errores" style="margin: 0 0 10px; font-size:1.5rem;">Revisa los errores</h2>
        <ul style="margin: 0 0 0 20px; padding:0;">
            @foreach ($errors->messages() as $campo => $mensajes)
                <li><a href="#{{ $campo }}" style="color:#ffffff;">{{ $mensajes[0] }}</a></li>
            @endforeach
        </ul>
    </section>
 @endif

 {{-- FORMULARIO --}}
    <section aria-labelledby="titulo-formulario" style="{{ $estiloSeccion }}">
        <h2 id="titulo-formulario" style="{{ $estiloH2 }}">Datos personales</h2>

        <form action="{{ route('qr.generar') }}" method="POST" id="formulario-qr" style="margin:0;">

            {{-- Token CSRF, obligatorio en formularios POST, si no, 419 Page Expired --}}
            @csrf

            <p style="margin: 0 0 18px;">Los campos marcados con <span aria-hidden="true" style="{{ $estiloAsterisco }}">*</span> son obligatorios.</p>

            {{-- NOMBRE --}}
            <div style="{{ $estiloCampo }}">
                <label for="nombre" style="{{ $estiloLabel }}">Nombre <span aria-hidden="true" style="{{ $estiloAsterisco }}">*</span></label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" autocomplete="given-name" required aria-required="true"
                        @error('nombre') aria-invalid="true" aria-describedby="error-nombre" @enderror
                        style="{{ $estiloInput }} @error('nombre') {{ $bordeError }} @else {{ $bordeNormal }} @enderror">
                @error('nombre')
                    <p id="error-nombre" style="{{ $estiloError }}">{{ $message }}</p>
                @enderror
            </div>

            {{-- APELLIDOS --}}
            <div style="{{ $estiloCampo }}">
                <label for="apellidos" style="{{ $estiloLabel }}">Apellidos <span aria-hidden="true" style="{{ $estiloAsterisco }}">*</span></label>
                <input type="text" name="apellidos" id="apellidos" value="{{ old('apellidos') }}" autocomplete="family-name" required aria-required="true"
                        @error('apellidos') aria-invalid="true" aria-describedby="error-apellidos" @enderror
                        style="{{ $estiloInput }} @error('apellidos') {{ $bordeError }} @else {{ $bordeNormal }} @enderror">
                @error('apellidos')
                    <p id="error-apellidos" style="{{ $estiloError }}">{{ $message }}</p>
                @enderror
            </div>

            {{-- DNI --}}
            <div style="{{ $estiloCampo }}">
                <label for="dni" style="{{ $estiloLabel }}">DNI <span aria-hidden="true" style="{{ $estiloAsterisco }}">*</span></label>
                <input type="text" name="dni" id="dni" value="{{ old('dni') }}" required aria-required="true"
                        @error('dni') aria-invalid="true" aria-describedby="error-dni" @enderror
                        style="{{ $estiloInput }} @error('dni') {{ $bordeError }} @else {{ $bordeNormal }} @enderror">
                @error('dni')
                    <p id="error-dni" style="{{ $estiloError }}">{{ $message }}</p>
                @enderror
            </div>

            {{-- BOTON --}}
            <button type="submit" style="display:block; box-sizing:border-box; width:100%; margin:10px 0 0; padding:12px; border:none; border:radius:5px; background-color:#7595ba; color:#0f2740; font-size:1rem; font-weight:bold; cursor:pointer;">Generar código QR</button>
        </form>
    </section>

{{-- QR --}}
@if ($datos)
    <section id="resultado-qr" tabindex="-1" aria-labelledby="titulo-resultado" style="{{ $estiloSeccion }}">
        <h2 id="titulo-resultado" style="{{ $estiloH2 }}">Código QR</h2> 

        {{-- aqui JS mete el QR en SVG --}}
        <div id="codigo-qr" 
                data-nombre="{{ $datos['nombre'] }}"
                data-apellidos="{{ $datos['apellidos'] }}"
                data-dni="{{ $datos['dni'] }}"
                style="display:flex; justify-content:center; align-items:center; margin:20px 0;">
        </div>

        {{-- Oculto contenido del QR --}}
        <div id="datos-accesibles" style="{{ $estiloOculto }}">
            <p id="mensaje">Codigo QR generado correctamentge.</p>
            <h3> Contenido del QR</h3>
            <p id="texto-datos">
                Nombre: {{ $datos['nombre'] }}<br>
                Apellidos: {{ $datos['apellidos'] }}<br>
                DNI: {{ $datos['dni'] }}
            </p>
        </div>
    </section>
@endif

@endsection

@section('scripts')
    <script type="module" src="{{ asset('js/script.js') }}"></script>
@endsection

