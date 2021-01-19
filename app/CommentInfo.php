<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentInfo extends Model
{
    use SoftDeletes;
    protected $table = 'comment_infos';
    protected $fillable = [
        'comment_id', 'likes', 'reply'
    ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];

}
