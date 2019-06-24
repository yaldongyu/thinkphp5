<?php

namespace app\admin\controller\housing;
use app\admin\model\Lphxpic as LphxpicModel;
use app\common\controller\Backend;
use think\Session;
/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Lphxpic extends Backend
{
    
    /**
     * Lphxpic模型对象
     * @var \app\admin\model\Lphxpic
     */
    protected $model = null;
    protected $noNeedRight = ['*'];
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Lphxpic;
        $this->view->assign("HxtypeList", LphxpicModel::getHxTypeList());
        $this->view->assign("XstypeList", LphxpicModel::getXsTypeList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    public function index($ids = NULL){
            //如果发送的来源是Selectpage，则转发到Selectpage
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->where('KP_LpID',$ids)
                    ->where($where)
                    ->order("KP_Px asc")
                    ->count();

            $list = $this->model
                    ->where($where)
                    ->where('KP_LpID',$ids)
                    ->order("KP_Px asc")
                    ->limit($offset, $limit)
                    ->select();
        $this->assign('lpname',db('lp')->where("id",$ids)->value('KP_LpName'));
        $this->assign('list', $list);
        $this->assign('count', $total);
        Session::set('lpid',$ids);
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
                    //$params['KP_KpTime'] = date("Y-m-d H:i",$params['KP_KpTime']);
                    // $params['KP_RzTime'] = date("Y-m-d H:i",$params['KP_RzTime']);
                    $params['KP_Px'] = $this->model->where('KP_LpID',Session::get('lpid'))->max('KP_Px')+1;
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
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    public function move(){
         $params = $this->request->post();
         $lpid = Session::get('lpid');
         if ($params['flag']==0) {
            $tmp = $this->model->where('id',$params['id'])->value('KP_Px');
            $tmp2 = $this->model->where(['KP_LpID'=>$lpid,'KP_Px'=>$tmp-1])->value('id');
            if ($tmp2) {
                $this->model->where('id', $params['id'])->setDec('KP_Px');
                $this->model->where('id', $tmp2)->setInc('KP_Px');
            }
         }else{
            $tmp = $this->model->where('id',$params['id'])->value('KP_Px');
            $tmp2 = $this->model->where(['KP_LpID'=>$lpid,'KP_Px'=>$tmp+1])->value('id');
            if ($tmp2) {
                $this->model->where('id', $params['id'])->setInc('KP_Px');
                $this->model->where('id', $tmp2)->setDec('KP_Px');
            }
         }

         return json(array("code" => 1));  
    }

}
