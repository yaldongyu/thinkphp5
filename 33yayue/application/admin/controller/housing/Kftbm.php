<?php

namespace app\admin\controller\housing;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Kftbm extends Backend
{
    
    /**
     * Kftbm模型对象
     * @var \app\admin\model\Kftbm
     */
    protected $model = null;
    protected $searchFields = 'id,KP_Name,KP_Phone,KP_Title';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Kftbm;

    }
    
    

}
