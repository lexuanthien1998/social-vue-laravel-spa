<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable;
    use SoftDeletes;
    protected $table = 'users';
    protected $fillable = [
        'name', 'email', 'password'
    ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function posts() {
        return $this->hasMany('App\Posts', 'user_id');
    }
}
