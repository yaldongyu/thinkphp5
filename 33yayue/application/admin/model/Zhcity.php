<?php

namespace app\admin\model;

use think\Model;

class Zhcity extends Model
{
    // 表名
    protected $name = 'zhcity';
    

    // 追加属性
    protected $append = [

    ];



    public function zhcity()
    {
        return $this->belongsTo('zhcity', 'ID','CUPID')->setEagerlyType(0);
    }
}
