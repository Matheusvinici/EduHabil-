<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InclusivaController extends Controller
{
    public function index()
    {
        return view('inclusiva.dashboard'); 
    }
}