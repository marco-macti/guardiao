<?php

use Illuminate\Database\Seeder;
use App\Cliente as OldCliente;
use App\Models\Cliente;

class ClientesSeeder extends Seeder
{
    public function run()
    {
        //$antigosClientes = OldCliente::all();

        //foreach ($antigosClientes as $key => $oldX) {

            /*Cliente::create([
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
            ]);*/

        //}

        Cliente::create([
            'cep'                  => '3280944',
            'endereco'             => 'Rua Sagrado Coracao de Maria',
            'numero'               => '67',
            'complemento'          => 'Casa A',
            'bairro'               => 'Santa Cecília',
            'cidade'               => 'Esmeraldas',   
            'estado'               => 'MG', 
            'cnpj'                 => '29692093000144', 
            'nome_fantasia'        => 'Green Signal',
            'razao_social'         => 'Green Signal Softwares LTDA',
            'inscricao_estadual'   => '9999999999',
            'ativo'                => 'S',
            'nome_do_responsavel'  => 'Bruno Santos da Fonseca',
            'tel1'                 => '031994570974',
            'tel2'                 => '031983316820',
            'email_cliente'        => 'contato@greensignal.com.br',
            'dt_nascimento'        => '1992-12-23',
            'operacao'             => 'Venda',
            'estado_destino'       => 'MG',
            'anotacoes'            => 'Melhor empresa do brasil.',
            'numero_de_lotes'      => 0          
        ]);
    }
}
