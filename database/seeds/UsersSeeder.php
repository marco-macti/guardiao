<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        \Schema::disableForeignKeyConstraints();

        $users = DB::connection('old')->select("SELECT * FROM usuario");
        
        foreach ($users as $key => $user) {
 
            $name = !empty($user->first_name) ? $user->first_name : explode('@',$user->email)[0];
 
            User::create([
             'cliente_id'   => $user->cliente_fk_id,
             'is_superuser' => $user->is_active,
             'is_staff'     => $user->is_staff,
             'is_active'    => $user->is_active,
             'confirmed'    => $user->confirmed,
             'name'         => $name,
             'email'        => $user->email,
             'password'     => \Hash::make('123456789')
            ]);
        }
 
        \Schema::enableForeignKeyConstraints();
 
    }
}
