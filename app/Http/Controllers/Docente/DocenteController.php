<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DocenteController extends Controller
{
    public function index()
    {
        return view('docente.index');
    }
}
