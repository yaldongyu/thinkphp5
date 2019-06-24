<?php

namespace app\admin\model;

use think\Model;

class Admin extends Model
{
    // 表名
    protected $name = 'admin';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'logintime_text'
    ];
    

    



    public function getLogintimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['logintime']) ? $data['logintime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setLogintimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


public function zw()
    {
        return $this->belongsTo('zw', 'KP_Zhiwu')->setEagerlyType(0);
    }

}
