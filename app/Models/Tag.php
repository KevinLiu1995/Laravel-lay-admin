<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
	use SoftDeletes;

    protected $guarded = ['id'];

    //与资讯多对多关联
    public function articles()
    {
        return $this->belongsToMany('App\Models\Article','article_tag','tag_id','article_id');
    }

}
