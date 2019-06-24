<?php

namespace app\admin\controller\sysattr;

use app\common\controller\Backend;
use think\Session;

/**
 * 成交房数据管理
 *
 * @icon fa fa-circle-o
 */
class Chengjiao extends Backend
{
    
    /**
     * Chengjiao模型对象
     * @var \app\admin\model\Chengjiao
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Chengjiao;

    }
    
    /**
     * 添加
     */
    public function add() 
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $params['adduser'] = Session::get('admin.nickname');
                    $params['addtime'] = date("Y-m-d H:i:s");
                    $params['edituser'] = Session::get('admin.nickname');
                    $params['edittime'] = date("Y-m-d H:i:s");
                    $result = $this->model->allowField(true)->save($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($this->model->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }
    

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        //print_r($row);
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
                   // $params['KP_KpTime'] = date("Y-m-d H:i:s");
                   // $params['KP_RzTime'] = date("Y-m-d H:i:s");
                    $params['edituser'] = Session::get('admin.nickname');
                    $params['edittime'] = date("Y-m-d H:i:s");
                    //print_r($params);exit;
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
      /*  $row['KP_Area_text'] = '';
        if ($row['areaid']) {
            $key = $row['areaid'];
            $keypid = -1;
            $str = '';
            while($keypid !=0) {
              $arr = db('zhcity')->where('ID',$key)->find();
              if ($str=='') {
                  $str = $arr['CNAME'];
              }else{
                  $str = $arr['CNAME'].'/'.$str;
              }
              
              $key = $arr['CUPID'];
              $keypid = $arr['CUPID'];
            } 
            $row['KP_Area_text'] =$str;
        }*/
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    

}
