# Generador de código QR (Laravel 11)

Para hacer este proyecto aplicando **Laravel 11**, reuse mi tarea anterior de Generador del codigo QR (hecho con: HTML, CSS, JS)

El usuario rellena un formulario con su nombre, apellidos y DNI o NIE y la página le genera un código QR con esos datos. Al escanearlo con el móvil sale el texto:

```text
Nombre: nombre introducido
Apellidos: apellidos introducidos
DNI/NIE: dni/nie introducido
```

## Cómo funciona

En el proyecto antiguo todo pasaba en el navegador: el JavaScript cogía los datos del formulario y generaba el QR. Ahora hay una parte que hace el servidor:

1. Al entrar en la página (`/`), Laravel muestra el formulario.
2. Al pulsar **Generar código QR**, el formulario se envía de verdad al servidor (`POST /qr`).
3. El controlador `QrController` comprueba que los datos son correctos.
   - Si hay algún error, Laravel vuelve al formulario, enseña los errores y mantiene lo que había escrito el usuario.
   - Si todo está bien, redirige otra vez a `/` y le pasa los datos guardándolos en la sesión solo para esa petición.
4. Blade pinta la página con los datos en unos atributos `data-*`.
5. El JavaScript lee esos atributos y genera el QR con la librería QRCode-JS.

He usado una redirección después del POST en vez de devolver la vista directamente porque, si no, al pulsar F5 el navegador pregunta si quieres reenviar el formulario. Buscando esto encontré que se llama patrón **PRG (Post/Redirect/Get)**.

## Tecnologías utilizadas

- Laravel 11 con PHP 8.3
- Blade para las vistas
- CSS inline (con atributos `style`, sin archivos `.css` ni Vite)
- JavaScript
- La librería QRCode-JS de Rabbit Company
- Git y GitHub

No hay base de datos.

## Instalación y ejecución

Hace falta tener PHP 8.3 y Composer.

```bash
git clone github.com/JuliaBosianek/formulario-qr-laravel11
cd formulario-qr-laravel11
composer install
cp .env.example .env
php artisan key:generate
```

En el `.env` hay que dejar la sesión y la caché en archivos, porque no uso base de datos:

```env
APP_LOCALE=es
SESSION_DRIVER=file
CACHE_STORE=file
```

Laravel necesita poder escribir en `storage` y `bootstrap/cache`:

```bash
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

Y para arrancarlo:

```bash
php artisan serve
```

Después se abre `http://localhost:8000` en el navegador.

Al principio pensaba configurar un VirtualHost en Apache, pero al final he usado `php artisan serve`, que es más sencillo para desarrollar. Si se usa Apache, hay que apuntar a la carpeta `public/` y no a la raíz del proyecto, porque si no se podría descargar el archivo `.env`.

**Una cosa sobre Laravel 11:** como ya no tiene soporte de seguridad, Composer no me dejaba instalarlo. Para poder usarlo (la práctica pide esta versión) tuve que añadir esto en `composer.json`:

```json
"policy": { "advisories": { "block": false } }
```

(en un proyecto real habría que usar una versión con soporte)

## Cómo lo he hecho

### Rutas y controlador

En `routes/web.php` hay dos rutas:

- `GET /`: muestra el formulario (`QrController@index`).
- `POST /qr`: valida los datos (`QrController@generar`).

He usado un controlador en vez de poner la lógica directamente en las rutas para que `web.php` quede más limpio.

### Validación

La validación la hace Laravel en el servidor con `$request->validate()`:

- **Nombre**: obligatorio y máximo 50 caracteres.
- **Apellidos**: obligatorios y máximo 100 caracteres.
- **DNI o NIE**: obligatorio, con formato de DNI (8 números y una letra, por ejemplo `12345678Z`) o de NIE (X, Y o Z, 7 números y una letra, por ejemplo `X1234567L`).

Al principio pensaba que la regla `string` comprobaba que el nombre fueran letras, pero probando vi que `123` pasaba (value del input es siempre tipo string). `string` solo comprueba el tipo de dato, así que añadí una expresión regular para que el nombre y los apellidos solo tengan letras. Me pregunté qué pasaría con nombres de otros países (Tailandia, Malasia...), así que la hice para que acepte letras de cualquier alfabeto, además de espacios, guiones, apóstrofos, puntos y barras:

```php
"regex:/^\pL[\pL\pM\s'.\/-]*$/u"
```

La del DNI/NIE:

```php
'regex:/^([0-9]{8}|[XYZxyz][0-9]{7})[A-Za-z]$/'
```

Como esta lleva el carácter `|`, las reglas tienen que ir en un **array** y no escritas como `'required|string|...'`, porque Laravel usa `|` para separar las reglas. Lo leí en la documentación de Laravel.

Los mensajes de error los he escrito en español dentro del controlador, porque Laravel 11 no trae la carpeta `lang` y los mensajes por defecto salen en inglés.

### Blade

Nunca había usado Blade. He hecho dos archivos:

- `resources/views/layouts/app.blade.php`: el layout, con la parte común de la página.
- `resources/views/formulario.blade.php`: el formulario, los errores y el resultado.

Las directivas que he usado son `@extends`, `@section` y `@yield` (para la plantilla), `@csrf` (el token del formulario), `@if` y `@foreach`, `@error` (para los errores de cada campo) y `@php`. Para imprimir datos uso `{{ }}`, que escapa el HTML. Así, si alguien escribe código en el nombre, no se ejecuta.

Los datos le llegan al JavaScript con atributos `data-*`:

```blade
<div id="codigo-qr"
     data-nombre="{{ $datos['nombre'] }}"
     data-apellidos="{{ $datos['apellidos'] }}"
     data-dni="{{ $datos['dni'] }}">
</div>
```

### CSS inline

Todo el CSS está en atributos `style` dentro de las etiquetas. Para no copiar los mismos estilos muchas veces, los guardé en variables con `@php` al principio de la vista. Al navegador le llega cada `style` completo igualmente.

Con CSS inline hay cosas que no se pueden hacer, así que tuve que cambiarlas:

- No se puede hacer `:hover` en el botón, así que lo quité.
- No se puede cambiar el estilo de `:focus`, así que uso el contorno que pone el navegador por defecto.
- No se pueden usar media queries, así que usé `clamp()` y `max-width: 100%` para que se adapte al móvil.
- El borde rojo de los campos con error lo pongo con `@error ... @else ... @enderror` dentro del `style`.
- El tamaño del QR lo pongo desde JavaScript, porque el SVG se crea después.

Revisando el contraste de los colores con el WebAIM Contrast Checker, vi que algunos colores del proyecto original no llegaban al mínimo (el texto del botón y el rojo del asterisco), así que los cambié por otros más claros u oscuros.

### JavaScript y la librería QRCode-JS

La librería está en `public/lib/qrcode.js` y mi script en `public/js/script.js`. Los puse en `public/` y no en `resources/` porque lo que está en `resources/js` solo llega al navegador si se compila con Vite, y en esta práctica no se usa.

Como ahora el formulario lo envía el servidor, quité el `preventDefault()` del script original. Ahora el script solo:

1. Lee los datos de los atributos `data-*`.
2. Genera el QR en SVG:

```javascript
const qr = QRCode.encode(datos);
const svg = qr.toSVG({
    scale: 8,
    title: "Código QR con datos personales"
});
```

3. Mueve el foco al resultado, o al resumen de errores si los hay.

Según su documentación, la librería sigue el estándar ISO/IEC 18004 de los códigos QR. Repositorio: https://github.com/Rabbit-Company/QRCode-JS

## Accesibilidad

He intentado mantener lo que ya tenía el proyecto original siguiendo WCAG 2.2, y he añadido algunas cosas:

- Cada campo tiene su `label` con `for`.
- Los campos obligatorios tienen `required` y `aria-required="true"`, y hay una nota que explica qué significa el asterisco ("Los campos marcados con * son obligatorios.").
- El DNI tiene un texto de ayuda con ejemplos del formato.
- Cuando hay errores:
  - el campo lleva `aria-invalid="true"` y `aria-describedby`, para que el lector de pantalla lea el mensaje;
  - sale un resumen arriba con enlaces a cada campo, y el foco va ahí;
  - cambia el título de la pestaña;
  - el error no se marca solo con color, también con un borde más grueso y el texto del mensaje.
- Cuando el QR se genera bien, el foco va a la sección del resultado.
- El SVG tiene un título.
- Hay un texto oculto con los datos del QR para las personas que usan lector de pantalla.
- Se puede usar todo con el teclado.

## Pruebas que he hecho

- **Envío correcto**: se genera el QR y al escanearlo con el móvil salen bien los datos, también con tildes y ñ.
- **F5 después de enviar**: no pregunta si quiero reenviar el formulario.
- **Validación en el servidor**: quité el `required` con F12 (o añadí `novalidate` al formulario) y envié campos vacíos, solo espacios, textos demasiado largos y números en el nombre. Laravel muestra el error en todos los casos.
- **DNI y NIE**: probé formatos incorrectos (7 números, sin letra, dos letras, con guion...) y correctos, en mayúscula y minúscula.
- **Seguridad**: escribí `<script>alert(1)</script>` en el nombre y no se ejecutó. Si borro el token del formulario con F12, Laravel da error 419.
- **Teclado**: el orden del tabulador es correcto, se puede enviar con Enter y el foco va bien a los errores y al resultado.
- **Zoom**: con el navegador al 200 % y al 400 % se sigue viendo todo sin scroll horizontal.

## Problemas que me encontré

- Me faltaba un `]` en la expresión regular del DNI y rechazaba DNI correctos. Lo encontré probando la expresión con `php artisan tinker`.
- Puse una coma en vez de un punto en `svg.style.maxWidth` y el QR no se generaba. En la consola salía un `ReferenceError`. Aprendí que los scripts con `type="module"` funcionan en modo estricto.
- Algunos mensajes de error salían en inglés porque no había escrito el mensaje de todas las reglas.
- Había escrito `area-labelledby` en vez de `aria-labelledby`.
- En la consola salía un error 404 de `favicon.ico`. Lo quité con `<link rel="icon" href="data:,">`.
- En el proyecto original, el texto del QR tenía espacios de más por la sangría del código. Ahora uno las líneas con `"\n"`.

## Cosas que se podrían mejorar

- Comprobar que la letra del DNI o NIE es la correcta (algoritmo del módilo 23), no solo el formato.
- Aceptar pasaportes u otros documentos.
- Con CSS inline no he podido poner estilos de `:hover` ni de `:focus`.
- Pasar a una versión de Laravel que tenga soporte.

## Estructura del proyecto

Estos son los archivos que he creado o modificado:

```text
formulario-qr-laravel11/
├── app/Http/Controllers/
│   └── QrController.php
├── public/
│   ├── js/
│   │   └── script.js
│   └── lib/
│       └── qrcode.js
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php
│   └── formulario.blade.php
├── routes/
│   └── web.php
└── README.md
```

## Autoría

Julia Bosianek — 2º DAW