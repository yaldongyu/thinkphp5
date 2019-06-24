<?php

namespace app\admin\model;

use think\Model;

class Ysxkz extends Model
{
    // 表名
    protected $name = 'ysxkz';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'KP_Time_text'
    ];
    

    



    public function getKpTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['KP_Time']) ? $data['KP_Time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setKpTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
