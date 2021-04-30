<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'dados.cnpj' => 'required|max:18|min:18',
            'dados.inscricao_estadual' => 'required|max:9|min:9',
            'dados.email_cliente' => 'required|email',
            'dados.operacao' => 'required',
            'dados.estado_destino' => 'required',
            'dados.razao_social' => 'required',
            'dados.nome_fantasia' => 'required',
            'dados.nome_do_responsavel' => 'required',
            'dados.tel1' => 'required',

            'dados.cep' => 'required|max:9|min:9',
            'dados.endereco' => 'required',
            'dados.endereco' => 'required',
            'dados.bairro' => 'required',
            'dados.cidade' => 'required',
            'dados.estado' => 'required',
        ];
    }
}
