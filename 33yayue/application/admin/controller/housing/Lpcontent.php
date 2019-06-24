<?php

namespace app\admin\controller\housing;

use app\common\controller\Backend;
use app\admin\model\Lpcontent as LpModel;
use think\Session;
/**
 * 楼盘内容表
 *
 * @icon fa fa-circle-o
 */
class Lpcontent extends Backend
{
    
    /**
     * Lpcontent模型对象
     * @var \app\admin\model\Lpcontent
     */
    protected $model = null;
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Lpcontent;
        $this->view->assign("typeList", LpModel::getTypeList());
    }
    
     public function index($ids = NULL)
    {
        //设置过滤方法 
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->where('KP_LpID',$ids)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where('KP_LpID',$ids)
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $list = collection($list)->toArray();
            $arr = LpModel::getTypeList();
            foreach ($list as $k => &$v)
            {
                $v['KP_Lm'] = $arr[$v['KP_Lm']];
            }
            unset($v);
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        Session::set('lpid',$ids);
        $this->assignconfig('id', ['name'=>$ids]);
        return $this->view->fetch();
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

                    $has = $this->model->where('KP_LpID',Session::get('lpid'))->where('KP_Lm',$params['KP_Lm'])->find();
                    if ($has) {
                        $this->error('该项目数据已存在');
                    }

                    $params['KP_LpID'] = Session::get('lpid');
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
                    $result = $row->allowField(true)->save($params);
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
        $this->assignconfig("row", $row);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    

}
