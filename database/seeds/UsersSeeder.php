<?php

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\User;

class UsersSeeder extends Seeder
{
    public function run()
    {

        User::create([
            'cliente_id'   => Cliente::first()->id,
            'is_superuser' => 'Y',
            'is_staff'     => 'N',
            'is_active'    => 'Y',
            'confirmed'    => 'Y',
            'name'         => 'Administrador',
            'email'        => 'admin@guardiao.com.br',
            'password'     => \Hash::make('123456789')
        ]);

    }
}
