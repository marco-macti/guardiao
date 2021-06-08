<?php

namespace App\Http\Controllers;

use App\Helpers\Cosmos;
use App\Http\Controllers\IA\IaController;
use App\Mail\NovoCadastro;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
    public function index(Request $request)
    {

        /*$ia_instance = new IaController();

        $reponse = $ia_instance->retornaDadosIa("ABRID. DE LATA E GARRAFA CLINCK 9CM","82055100");

        dd($reponse);
        */

        return view('home');

    }
}
