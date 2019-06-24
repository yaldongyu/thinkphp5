<?php

namespace app\admin\controller\housing;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Lpcomment extends Backend
{
    
    /**
     * Lpcomment模型对象
     * @var \app\admin\model\Lpcomment
     */
    protected $model = null;
    protected $relationSearch = true;
    protected $searchFields = 'KP_Content';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Lpcomment;

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function index($ids = 0){
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            { 
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            if ($ids!=0) {
                $total = $this->model
                    ->with(["lp"])
                    ->where('KP_LpID',$ids)
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->with(["lp"])
                    ->where($where)
                    ->where('KP_LpID',$ids)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            }else{
                $total = $this->model
                    ->with(["lp"])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->with(["lp"])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            }
            
            /*foreach ($list as $k => &$v)
            {
                $v['KP_PText'] = $this->model->where('ID',$v['KP_Upid'])->value('KP_BmName');;
            }
            unset($v);*/
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        $this->assignconfig('id', ['name'=>$ids]);
        return $this->view->fetch();
    }

}
