<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoordenadorController extends Controller
{
    /**
     * Exibe o painel do coordenador.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        return view('coordenador.dashboard'); // Certifique-se de que a view `coordenador.dashboard` existe
    }
}