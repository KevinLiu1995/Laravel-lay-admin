<?php
namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;

class Permission extends \Spatie\Permission\Models\Permission
{
	use SoftDeletes;

    protected $appends = ['type_name'];

    public function getTypeNameAttribute()
    {
        return $this->attributes['type_name'] = Arr::get([1=>'按钮',2=>'菜单'],$this->type);
    }

    //子权限
    public function childs()
    {
        return $this->hasMany('App\Models\Permission','parent_id','id');
    }

    //所有子权限递归
    public function allChilds()
    {
        return $this->childs()->with('allChilds');
    }

}
