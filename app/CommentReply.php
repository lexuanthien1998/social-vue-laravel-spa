<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentReply extends Model
{
    use SoftDeletes;
    protected $table = 'comment_replies';
    protected $fillable = [
        'user_id', 'reply'
    ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];
}
