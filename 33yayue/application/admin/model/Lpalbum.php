<?php

namespace app\admin\model;

use think\Model;

class Lpalbum extends Model
{
    // 表名
    protected $name = 'lpalbum';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [

    ];
    

    
 public static function getXcTypeList()
    {
        $typeList = config('site.LpXcLx');
        foreach ($typeList as $k => &$v)
        {
            $v = __($v);
        }
        return $typeList;
    }






}
