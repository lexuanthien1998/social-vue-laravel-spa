<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contacts extends Model
{
    use SoftDeletes;
    protected $table = 'contacts';
    protected $fillable = [
        'from', 'to'
    ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
