<?php

namespace app\admin\model;

use think\Model;


class Lptg extends Model
{
    // 表名
    protected $name = 'lptg';
    
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
        return $this->belongsTo('Lp', 'KP_LpID', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    /**
     * 读取站点分类类型
     * @return array
     */
    public static function getTypeList()
    {
        $typeList = config('site.Zhandian');
        foreach ($typeList as $k => &$v)
        {
            $v = __($v);
        }
        return $typeList;
    }

    /**
     * 读取楼盘状态0 进行中
     * @return array
     */
    public static function getLptgztList( )
    {
        $lptgztList = config('site.LptgZhuangTai');
        foreach ($lptgztList as $k => &$v)
        {
            $v = __($v);
        }
        return $lptgztList;
 
    }

}
