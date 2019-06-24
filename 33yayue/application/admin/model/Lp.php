<?php

namespace app\admin\model;

use think\Model;

class Lp extends Model
{
    // 表名
    protected $name = 'lp';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
 /*   protected $append = [
        'KP_KpTime_text',
        'KP_RzTime_text'
    ];
    */

    protected static function base($query){   // 5.0.2版本之前需要使用static定义
        $query->where('KP_Jz',0);
    }


/*    public function getKpKptimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['KP_KpTime']) ? $data['KP_KpTime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getKpRztimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['KP_RzTime']) ? $data['KP_RzTime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setKpKptimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setKpRztimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }*/

    /**
     * 读取分类类型
     * @return array
     */
    public static function getTypeList()
    {
        $typeList = config('site.zxzt');
        foreach ($typeList as $k => &$v)
        {
            $v = __($v);
        }
        return $typeList;
    }

    public function lpprice()
    {
        return $this->belongsTo('lpprice', 'id','KP_LpID')->setEagerlyType(0);
    }

    public function zhcity()
    {
        return $this->belongsTo('zhcity', 'KP_City','id')->setEagerlyType(0);
    }

}
