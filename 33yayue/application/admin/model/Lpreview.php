<?php

namespace app\admin\model;

use think\Model;

class Lpreview extends Model
{
    // 表名
    protected $name = 'lpreview';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [

    ];
    

    



public function lp()
    {
        return $this->belongsTo('lp', 'KP_LpID','id','')->setEagerlyType(0);
    }



}
