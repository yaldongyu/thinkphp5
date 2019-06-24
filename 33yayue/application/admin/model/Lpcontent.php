<?php

namespace app\admin\model;

use think\Model;

class Lpcontent extends Model
{
    // 表名
    protected $name = 'lpcontent';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [

    ];
    

    
     /**
     * 读取分类类型
     * @return array
     */
    public static function getTypeList()
    {
        $typeList = config('site.lpcontenttype');
        foreach ($typeList as $k => &$v)
        {
            $v = __($v);
        }
        return $typeList;
    }






}
