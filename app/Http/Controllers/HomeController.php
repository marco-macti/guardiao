<?php

namespace App\Http\Controllers;

use App\Http\Controllers\IA\IaController;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        /*$ia_instance = new IaController();\
        $response = $ia_instance->retornaDadosIa('REFRI FANTA 2L LA','22021000');
        dd($response);*/

        return view('home');
    }
}
