<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 测试表
 *
 * @icon fa fa-circle-o
 */
class Test extends Backend
{
    
    /**
     * Test模型对象
     * @var \app\admin\model\Test
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Test;
        $this->view->assign("weekList", $this->model->getWeekList());
        $this->view->assign("flagList", $this->model->getFlagList());
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        $this->view->assign("hobbydataList", $this->model->getHobbydataList());
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("stateList", $this->model->getStateList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    public function index(){
        set_time_limit(0);
        //$arr0 = db('zhcity')->where("CLEVEL",1)->field("ID,CNAME")->select();
        /*$arr1 = db('zhcity')->where("CLEVEL",1)->field("ID,CNAME")->select();
        $arr2 = db('zhcity')->where("CLEVEL",2)->field("ID,CNAME")->select();
        $arr3 = db('zhcity')->where("CLEVEL",3)->field("ID,CNAME")->select();
        $db = db('zhcity');
        foreach ($arr1 as $key => $value) {
            $arr11 = $db->where(["CLEVEL"=>2,"CUPID"=>$value['ID']])->field("ID,CNAME")->select();
            foreach ($arr11 as $key => $val) {
                $ct[$value['ID']*10000][$val['ID']*10000] = $val['CNAME'];
            }
            
        }
        foreach ($arr2 as $key => $value) {
            $arr22 = $db->where(["CLEVEL"=>3,"CUPID"=>$value['ID']])->field("ID,CNAME")->select();
            foreach ($arr22 as $key => $val) {
                $ct[$value['ID']*10000][$val['ID']*10000] = $val['CNAME'];
            }
            
        }
        
        foreach ($arr3 as $key => $value) {
            $arr33 = $db->where(["CLEVEL"=>4,"CUPID"=>$value['ID']])->field("ID,CNAME")->select();
            foreach ($arr33 as $key => $val) {
                $ct[$value['ID']*10000][$val['ID']*10000] = $val['CNAME'];
            }
            
        }*/

        return json($ct);
    }

    /*public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        print_r($row);exit;
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    if ($params['lb']==1) {
                        $params['KP_Tj'] = 1;
                    }elseif($params['lb']==2){
                        $params['KP_Waphd'] = 1;
                    }else{
                        $params['KP_Ontop'] = 1;
                    }
                    unset($params['lb']);
                    $params['KP_EditUser'] = Session::get('admin.nickname');
                    $params['KP_EditTime'] = date("Y-m-d H:i:s");
                    $result = $row->allowField(false)->save($params);
                    //echo $row->getlastsql(); 
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }*/
}
