<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QrController extends Controller {

    //muestra formulario vacío
    public function index(){

        return view('qr.index');
    }

    //valida los datos y devuelve la vista con el texto del QR
    public function generar(Request $request){

        $validados = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'apellidos' => ['required', 'string', 'max:100'],
            'dni' => ['required', 'regex:/^[0-9{8}[A-Za-z]$/'],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'dni.regex' => 'El DNI debe tener 8 números y una letra (ej: 12345678Z',
        ]);

        $datos = "Nombre: {$validados['nombre']}\n"
                . "Apellidos: {$validados['apellidos']}\n"
                . "DNI: " . strtoupper($validados['dni']);
                
        return view('qr.index', ['datos' => $datos]);
    }
}