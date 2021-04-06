<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cliente;

class LotesController extends Controller
{
    public function index(){

        $clientes = Cliente::all();

        return view('admin.lotes.index')->with('clientes',$clientes);
    }

    public function edit($lote){
        return view('admin.lotes.produtos');
    }
}
