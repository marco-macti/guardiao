<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Sanitize;
use Illuminate\Support\Facades\Hash;

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

    public function detalhesCliente(Request $request, $id)
    {
        $id = decrypt($id);
        $data['cliente'] = Cliente::find($id);

        if(!$data['cliente'])
            return back()->withErrors("Cliente não localizado!");

        if($request->has('page'))
        {
            $page = $request->get('page');
            $modulo = $request->get('modulo');
            $current_page = $request->get('current_page');

            switch ($modulo) {
                case 'user':
                    $data['lotes'] = Lote::where('cliente_id', $id)->paginate(10, ['*'], 'page', $current_page);
                    $data['usuarios'] = User::where('cliente_id', $id)->paginate(10, ['*'], 'page', $page);
                    break;
                case 'lote':
                    $data['lotes'] = Lote::where('cliente_id', $id)->paginate(10, ['*'], 'page', $page);
                    $data['usuarios'] = User::where('cliente_id', $id)->paginate(10, ['*'], 'page', $current_page);
                    break;
            }
        }else{
            $data['lotes'] = Lote::where('cliente_id', $id)->paginate(10);
            $data['usuarios'] = User::where('cliente_id', $id)->paginate(1);
        }
        
        return view('admin.clientes.detalhes',$data);
    }

    public function adduser(Request $request)
    {
        $dados = $request->get('dados');

        $verificaCliente = User::where('email', $dados['email'])->where('cliente_id', $dados['cliente_id'])->first();

        if($verificaCliente)
            return back()->withErrors('O usuário informado ja está cadastrado no Cliente!');

        $dados['is_superuser'] = 'N';
        $dados['is_staff'] = 'Y';
        $dados['is_active'] = 'Y';
        $dados['confirmed'] = 'Y';
        $dados['password'] = Hash::make('123456789');

        $create = User::create($dados);
        
        if(!$create)
            return back()->withErrors('Falha ao cadastrar novo usuário!');

        return back()->withSuccess('Usuário cadastrado com sucesso!');
    }

    public function removeUser($id)
    {
        $user = User::find(decrypt($id));

        if(!$user)
            return back()->withErrors("Usuário não localizado!");

        $delete = $user->delete();

        if(!$delete)
            return back()->withErrors("Falha ao excluir usuario!");

        return back()->withSuccess("Usuário excluído com sucesso!");
    }

    public function infoUser(Request $request)
    {
        $user = User::find(decrypt($request->get('id')));

        if(!$user)
            return response()->json(["msg" => "Usuário não localizado!"], 400);

        return response()->json(["user" => $user], 200);
    }

    public function edituser(Request $request)
    {
        $dados = $request->get('dados');
        $user = User::find($dados['user_id']);

        if(!$user)
            return response()->withErrors("Usuário não localizado!");

        $user->update([
            'name' => $dados['name'],
            'email' => $dados['email'],
        ]);

        return back()->withSuccess("Usuário editado com sucesso!");
    }
}
