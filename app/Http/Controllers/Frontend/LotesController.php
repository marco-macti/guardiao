<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LotesController extends Controller
{
    public function index(){
        return view('frontend.lotes.index');
    }

    public function edit($lote){
        return view('frontend.lotes.produtos');
    }
    
}
