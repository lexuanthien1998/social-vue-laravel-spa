<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentLikes extends Model
{
    use SoftDeletes;
    protected $table = 'comment_likes';
    protected $fillable = [
        'user_id'
    ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];

}
