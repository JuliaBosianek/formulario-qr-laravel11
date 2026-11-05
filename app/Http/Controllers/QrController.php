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
            'nombre' => ['required', 'string', 'max:50'],
            'apellidos' => ['required', 'string', 'max:100'],
            'dni' => ['required', 'string', 'regex:/^[0-9]{8}[A-Za-z]$/'],
        ], 
        [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'dni.regex' => 'El DNI debe tener 8 números y una letra (ej: 12345678Z)',
        ],
        [
            'nombre' => 'nombre',
            'apellidos' => 'apellidos',
            'dni' => 'DNI'
        ]
        );

        $validados['dni'] = strtoupper($validados['dni']);

        //Patrón PRG: POST válida, redirige al GET y pasa los datos x la sesión con ->with() cual guarda los datos solo para la siguiente peticion     
        return redirect()
            ->route('qr.index')
            ->with('datos', $validados);
    }
}