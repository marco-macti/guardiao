<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

//Models
use App\Models\Cliente;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = [];

    protected $hidden = [ 'password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }
}
