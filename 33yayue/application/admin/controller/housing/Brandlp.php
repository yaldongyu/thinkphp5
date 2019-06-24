<?php

namespace app\admin\controller\housing;

use app\common\controller\Backend;
use think\Session;
use think\Db;
/**
 * 品牌楼盘管理
 *
 * @icon fa fa-circle-o
 */
class Brandlp extends Backend
{
    
    /**
     * Brandlp模型对象
     * @var \app\admin\model\Brandlp
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Brandlp;

    }


    public function index(){
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            foreach ($list as $k => &$v)
            {
                $ids = $v['KP_Brand'];
                if ($ids) {
                   $str = Db::query("SELECT GROUP_CONCAT(KP_LpName) FROM kp_lp where id in ($ids)");
                   $v['KP_Brand'] = $str[0]['GROUP_CONCAT(KP_LpName)'];
                }else{
                    $v['KP_Brand'] = '';
                }
                
            }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }
    
    public function selectlp(){

        return $this->view->fetch();
    }
    
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

                    $params['weigh'] = $this->model->max("weigh")+1;    

                    $params['KP_AddUser'] = Session::get('admin.nickname');
                    $params['KP_AddTime'] = date("Y-m-d H:i:s");
                    $params['KP_EditUser'] = Session::get('admin.nickname');
                    $params['KP_EditTime'] = date("Y-m-d H:i:s");
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
        $row['KP_City'] = '';
        $pr = db('zhcity')->where('id',$row['KP_Provice'])->value('CNAME');
        $ci = db('zhcity')->where('id',$row['KP_Citys'])->value('CNAME');
        $di = db('zhcity')->where('id',$row['KP_District'])->value('CNAME');
        $co = db('zhcity')->where('id',$row['KP_County'])->value('CNAME');
        if ($pr) {
          $row['KP_City'] = $pr;
        }
        if ($ci) {
          $row['KP_City'] = $pr.'/'.$ci;
        }
        if ($di) {
          $row['KP_City'] = $pr.'/'.$ci.'/'.$di;
        }
        if ($co) {
          $row['KP_City'] = $pr.'/'.$ci.'/'.$di.'/'.$co;
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

}
