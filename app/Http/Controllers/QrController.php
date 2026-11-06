<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QrController extends Controller {

    //muestra formulario vacío GET
    public function index(){

        $datos = session('datos');

        return view('formulario',['datos' => $datos]);
    }

    //valida los datos y devuelve la vista con el texto del QR; POST
    public function generar(Request $request){

        $validados = $request->validate([
            'nombre' => ['required', 'string', 'max:50', "regex:/^\pL[\pL\pM\s'.\/-]*$/u"],
            'apellidos' => ['required', 'string', 'max:100', "regex:/^\pL[\pL\pM\s'.\/-]*$/u"],
            'dni' => ['required', 'string', 'regex:/^([0-9]{8}|[XYZxyz][0-9]{7})[A-Za-z]$/'],
        ], 
        [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.regex' => 'El nombre solo puede contener letras espacios, guiones, apostrofos, puntos o barras.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras espacios, guiones, apostrofos, puntos o barras.',
            'dni.required' => 'El DNI o NIE es obligatorio.',
            'dni.regex' => 'Introduce un DNI (8 números y una letra, ej.:12345678Z) o un NIE (X,Y o Z, 7 números y una letra, ej.: X1234567L).',
            'max' => 'El campo :attribute no puede tener más de :max caracteres.',
            'string' => 'El campo :attribute debe ser texto.',
        ],
        [
            'nombre' => 'nombre',
            'apellidos' => 'apellidos',
            'dni' => 'DNI o NIE'
        ]
        );

        $validados['dni'] = strtoupper($validados['dni']);

        //Patrón PRG: POST válida, redirige al GET y pasa los datos x la sesión con ->with() cual guarda los datos solo para la siguiente peticion     
        return redirect()
            ->route('qr.index')
            ->with('datos', $validados);
    }
}