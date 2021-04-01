<?php

use Illuminate\Database\Seeder;
use App\Cliente as OldCliente;
use App\Models\Cliente;

class ClientesSeeder extends Seeder
{
    public function run()
    {
        $antigosClientes = OldCliente::all();

        foreach ($antigosClientes as $key => $oldX) {

            Cliente::create([
                'cep'                  => $oldX->cep,
                'endereco'             => $oldX->rua,
                'numero'               => $oldX->numero,
                'complemento'          => $oldX->compl,
                'bairro'               => $oldX->bairro,
                'cidade'               => $oldX->cidade,   
                'estado'               => $oldX->estado, 
                'cnpj'                 => $oldX->cnpj, 
                'nome_fantasia'        => $oldX->nome_fantasia,
                'razao_social'         => $oldX->razao_social,
                'inscricao_estadual'   => $oldX->inscricao_estadual,
                'ativo'                => 'S',
                'nome_do_responsavel'  => $oldX->nome_do_responsavel,
                'tel1'                 => $oldX->tel1,
                'tel2'                 => $oldX->tel2,
                'email_cliente'        => $oldX->email_cliente,
                'dt_nascimento'        => $oldX->dt_nascimento,
                'operacao'             => $oldX->operacao,
                'estado_destino'       => $oldX->estado_destino,
                'anotacoes'            => $oldX->anotacoes,
                'numero_de_lotes'      => 0          
            ]);

        }
    }
}
