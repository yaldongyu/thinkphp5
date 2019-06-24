<?php

namespace app\admin\controller\housing;
use app\admin\model\Lpalbum as LpalbumModel;
use app\common\controller\Backend;
use think\Session;
/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Lppic extends Backend
{
    
    /**
     * Lppic模型对象
     * @var \app\admin\model\Lppic
     */
    protected $model = null;
    protected $noNeedRight = ['*'];
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Lppic;
        $this->view->assign("XctypeList", LpalbumModel::getXcTypeList());
    }
    
    public function index($ids = NULL){
            //如果发送的来源是Selectpage，则转发到Selectpage
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->where('KP_HcID',$ids)
                    ->where($where)
                    ->order($sort,$order)
                    ->count();

            $list = $this->model
                    ->where($where)
                    ->where('KP_HcID',$ids)
                    ->order($sort,$order)
                    ->limit($offset, $limit)
                    ->select();
        $this->assign('lpname',db('lpalbum')->where("id",$ids)->value('KP_HcName'));
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
                    $arr = LpalbumModel::getXcTypeList();
                    $params['KP_HcID'] = Session::get('lpid');
                    $params['KP_PicTitle'] = $arr[db('lpalbum')->where("id",$params['KP_HcID'])->value('KP_HcName')];
                    $params['KP_AddUser'] = Session::get('admin.nickname');
                    $params['KP_AddTime'] = date("Y-m-d");
                    $params['KP_EditUser'] = Session::get('admin.nickname');
                    $params['KP_EditTime'] = date("Y-m-d");
                    $urlarr = explode(',',$params['KP_PicUrl']);
                    foreach ($urlarr as $key=>$value) {
                        $params['KP_PicUrl'] = $value;
                        //$result = $this->model->allowField(true)->save($params);
                        $result = $this->model->insert($params);
                    }
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
                    $params['KP_EditTime'] = date("Y-m-d");
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


    public function covers(){
             $params = $this->request->post();

             $this->model->where('id',$params['id'])->where('KP_HcID',$params['hid'])->setField('KP_Covers', 1);
             $this->model->where('id','<>',$params['id'])->where('KP_HcID',$params['hid'])->setField('KP_Covers', 0);

             return json(array("code" => 1));  
        }

}
