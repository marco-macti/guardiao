<?php

namespace App\Http\Controllers;

use App\Helpers\Cosmos;
use App\Http\Controllers\IA\IaController;
use App\Mail\NovoCadastro;
use App\Models\Lote;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        if(\Auth::check()){

            $loggedUser = auth()->user();

            if($loggedUser->confirmed == 'N' && $loggedUser->is_superuser == 'N'){
                return redirect()->to('atualizar-senha');
            }
        }

        /*$ia_instance = new IaController();

        $reponse = $ia_instance->retornaDadosIa("ABRID. DE LATA E GARRAFA CLINCK 9CM","82055100");

        dd($reponse);
        */

        return view('home');

    }

    public function atualizarSenha(Request $request){

        if(empty($request->all())){

            return view('auth.confirm');

        }else{

            if($request->has('senha')){

                auth()->user()->update([
                    'password'  => bcrypt($request->senha),
                    'confirmed' => 'Y',
                    'is_active' => 'Y'
                ]);
            }

            return redirect()->to('/home');

        }

    }
}
