<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfessorController extends Controller
{
    public function dashboard()
    {
        return view('professor.dashboard'); // Crie a view `professor.dashboard`
    }
}
