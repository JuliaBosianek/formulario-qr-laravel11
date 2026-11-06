{{-- Layout principal--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Formulario que genera código QR')</title>
    <link rel="icon" href="data:,">
</head>
<body style="margin:0; padding:0; font-family:Arial, sans-serif; background-color:#234b72; color:#c1d3e5; line-height:1.5;">
    
    <header style="text-align:center; padding:30px 20px 20px;">
        {{-- clamp(minimo, preferido, maximo) - sustituye media query de 500px--}}
        <h1 style="margin:0; font-size:clamp(1.6rem, 5vw, 2rem);"> Formulario con código QR</h1>
    </header>

    <main style="width:90%; max-width:700px; margin:0 auto 40px;">
        @yield('contenido')
    </main>

    @yield('scripts')
</body>
</html>