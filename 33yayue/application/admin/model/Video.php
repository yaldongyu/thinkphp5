<?php

namespace app\admin\model;

use think\Model;

class Video extends Model
{
    // 表名
    protected $name = 'video';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [

    ];
    

    /**
     * 读取楼盘状态0 进行中
     * @return array
     */
    public static function getTypeList()
    {
        $lptgztList = config('site.Video_Type');
        foreach ($lptgztList as $k => &$v)
        {
            $v = __($v);
        }
        return $lptgztList;
 
    }


 public function lp()
    {
        return $this->belongsTo('lp', 'KP_LpID','id','',"left")->setEagerlyType(0);
    }




}
