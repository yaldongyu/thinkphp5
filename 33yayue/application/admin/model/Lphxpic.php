<?php

namespace app\admin\model;

use think\Model;

class Lphxpic extends Model
{
    // 表名
    protected $name = 'lphxpic';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [

    ];
    

    
    public static function getHxTypeList()
    {
        $typeList = config('site.Lp_HxType');
        foreach ($typeList as $k => &$v)
        {
            $v = __($v);
        }
        return $typeList;
    }


    public static function getXsTypeList()
    {
        $typeList = config('site.hxXsType');
        foreach ($typeList as $k => &$v)
        {
            $v = __($v);
        }
        return $typeList;
    }




}
