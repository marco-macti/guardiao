<?php

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClientesSeeder extends Seeder
{
    public function run()
    {
       
        Cliente::create([
            'cep'                  => '30180-070',
            'endereco'             => 'Rua Tenente Brito Melo',
            'numero'               => '476',
            'complemento'          => 'CONJ 701',
            'bairro'               => 'Barro Preto',
            'cidade'               => 'Belo Horizonte',   
            'estado'               => 'MG', 
            'cnpj'                 => '00000000000', 
            'nome_fantasia'        => 'Grupo GSV',
            'razao_social'         => 'Grupo GSV',
            'inscricao_estadual'   => '9999999999',
            'ativo'                => 'S',
            'nome_do_responsavel'  => 'André Souza',
            'tel1'                 => '031994570974',
            'tel2'                 => '031983316820',
            'email_cliente'        => 'contato@gsv.com.br',
            'dt_nascimento'        => '1992-12-23',
            'operacao'             => 'Venda',
            'estado_destino'       => 'MG',
            'anotacoes'            => 'Melhor empresa do brasil.',
            'numero_de_lotes'      => 0          
        ]);
    }
}
