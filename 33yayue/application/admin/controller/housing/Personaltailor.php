<?php

namespace app\admin\controller\housing;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Personaltailor extends Backend
{
    
    /**
     * Personaltailor模型对象
     * @var \app\admin\model\Personaltailor
     */
    protected $model = null;
    protected $searchFields = 'KP_Name,KP_Tel';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Personaltailor;

    }

    

}
