<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Sanitize;


//Models
use App\Models\Cliente;
use App\Models\Lote;
use App\User;

//Requests
use App\Http\Requests\ClienteRequest;

class ClientesController extends Controller
{
    public function index()
    {
        $data['clientes'] = Cliente::paginate(15);
        
        return view('admin.clientes.index', $data);
    }

    public function formulario()
    {
        $data['estados'] = [
            "AC" => "AC",
            "AL" => "AL",
            "AM" => "AM",
            "AP" => "AP",
            "BA" => "BA",
            "CE" => "CE",
            "DF" => "DF",
            "ES" => "ES",
            "GO" => "GO",
            "MA" => "MA",
            "MT" => "MT",
            "MS" => "MS",
            "MG" => "MG",
            "PA" => "PA",
            "PB" => "PB",
            "PR" => "PR",
            "PE" => "PE",
            "PI" => "PI",
            "RJ" => "RJ",
            "RN" => "RN",
            "RO" => "RO",
            "RS" => "RS",
            "RR" => "RR",
            "SC" => "SC",
            "SE" => "SE",
            "SP" => "SP",
            "TO" => "TO"
        ];

        return view('admin.clientes.formulario',$data);
    }

    public function cadastraCliente(ClienteRequest $request)
    {
        $dados = $request->get('dados');

        $dados['cnpj'] = Sanitize::sanitizeValueForMask($dados['cnpj']);

        $verificaCnpj = Cliente::where('cnpj', $dados['cnpj'])->first();

        if( $verificaCnpj)
            return back()->withErrors("O CNPJ informado encontra-se cadastrado!");

        $create = Cliente::create($dados);

        if(!$create)
            return back()->withErrors("Falha ao cadastrar cliente!");


        return back()->withSuccess("Cliente Cadastrado com sucesso!");

    }

    public function detalhesCliente($id)
    {
        $id = decrypt($id);
        
        $data['cliente'] = Cliente::find($id);

        if(!$data['cliente'])
            return back()->withErrors("Cliente não localizado!");

        $data['lotes'] = Lote::where('cliente_id', $id)->paginate(15);
        $data['usuarios'] = User::where('cliente_id', $id)->paginate(15);
        
        return view('admin.clientes.detalhes',$data);
    }
}
