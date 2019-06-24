<?php

namespace app\admin\controller\zixun;

use app\common\controller\Backend;
use app\admin\model\Lptg as LptgModel;
use app\admin\model\Newslm as NewslmModel;

use think\Session;
/**
 * 
 *
 * @icon fa fa-circle-o
 */ 
class News extends Backend
{
    
    /**
     * News模型对象
     * @var \app\admin\model\News
     */ 
    protected $model = null;
    protected $relationSearch = true;
    protected $searchFields = 'id,KP_Title,lp.KP_LpName,KP_Nr';
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\News;
        $newslmmodel = new NewslmModel();
        $this->view->assign('typeList',LptgModel::getTypeList());
        $this->view->assign('lbList',$newslmmodel->field('id,KP_Name')->where('KP_Stutas',1)->select());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    public function index(){
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            $cityid = $this->request->get("kpcity", 0);
            if ($cityid) {
                $citywhere = " news.KP_Provice=".$cityid." or news.KP_Citys=".$cityid." or news.KP_District=".$cityid." or news.KP_County=".$cityid;
            }else{
                $citywhere = '1=1';
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with(["lp"])
                    ->where($where)
                    ->where($citywhere)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(["lp"])
                    ->where($where)
                    ->where($citywhere)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            //$dzarr = LptgModel::getTypeList();
            $newslmmodel = new NewslmModel();
            $lbarr = $newslmmodel->field('id,KP_Name')->select();
            foreach ($list as $key => $value) {
                $list[$key]['KP_Lm_Name'] = $newslmmodel->where("id",$value['KP_Lmid'])->value('KP_Name');
                if ($value['KP_County']==0) {
                    if ($value['KP_District']==0) {
                        if ($value['KP_Citys']==0) {
                            if ($value['KP_Provice']==0) {
                                $list[$key]['area_text'] ='--';
                            }else{
                                $list[$key]['area_text'] = db('zhcity')->where('id',$value['KP_Provice'])->cache()->value('CNAME');
                            }
                        }else{
                            $list[$key]['area_text'] = db('zhcity')->where('id',$value['KP_Citys'])->cache()->value('CNAME');
                        }
                    }else{
                        $list[$key]['area_text'] = db('zhcity')->where('id',$value['KP_District'])->cache()->value('CNAME');
                    }
                }else{
                     $list[$key]['area_text'] = db('zhcity')->where('id',$value['KP_County'])->cache()->value('CNAME');   
                }
            }
            $result = array("total" => $total, "rows" => $list);
            
            return json($result);
        }

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
                    $params['KP_Tj'] = 0;
                    $params['KP_Waphd'] = 0;
                    $params['KP_Ontop'] = 0;
                    $params['KP_Zd'] = 0;
                    foreach ($params['lb'] as $value) {

                        if ($value==1) {
                            $params['KP_Tj'] = 1;
                        }elseif($value==2){
                            $params['KP_Waphd'] = 1;
                        }elseif($value==3){
                            $params['KP_Zd'] = 1;
                        }else{
                            $params['KP_Ontop'] = 1;
                        }
                    }
                    
                    unset($params['lb']);
                    $params['KP_Description'] = $params['KP_desc'];
                    $params['KP_AddUser'] = Session::get('admin.nickname');
                    $params['KP_AddTime'] = date("Y-m-d H:i:s");
                    $params['KP_EditUser'] = Session::get('admin.nickname');
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
                    foreach ($params['lb'] as $value) {
                        if ($value==1) {
                            $params['KP_Tj'] = 1;
                        }elseif($value==2){
                            $params['KP_Waphd'] = 1;
                        }elseif($value==3){
                            $params['KP_Zd'] = 1;
                        }else{
                            $params['KP_Ontop'] = 1;
                        }
                    }
                    unset($params['lb']);
                    $params['KP_EditUser'] = Session::get('admin.nickname');
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

        if ($row['KP_Ontop']==1) {
            $row['lb0'] =0;
        }else{
            $row['lb0'] =-1;
        }
        if ($row['KP_Tj']==1) {
            $row['lb1'] = 1;
        }else{
            $row['lb1'] =-1;
        }
        if ($row['KP_Waphd']==1) {
            $row['lb2'] = 2;
        }else{
            $row['lb2'] =-1;
        }
        if ($row['KP_Zd']==1) {
            $row['lb3'] = 3;
        }else{
            $row['lb3'] =-1;
        }
        $row['area_text'] = '';
        if ($row['KP_County']==0) {
            if ($row['KP_District']==0) {
                if ($row['KP_Citys']==0) {
                    if ($row['KP_Provice']==0) {
                        $row['area_text'] = '';
                    }else{
                        $pr = db('zhcity')->where('id',$row['KP_Provice'])->cache()->value('CNAME');
                        $row['area_text'] = $pr;
                    }
                }else{
                    $pr = db('zhcity')->where('id',$row['KP_Provice'])->cache()->value('CNAME');
                    $ci = db('zhcity')->where('id',$row['KP_Citys'])->cache()->value('CNAME');
                    $row['area_text'] = $pr.'/'.$ci;
                }
            }else{
                $pr = db('zhcity')->where('id',$row['KP_Provice'])->cache()->value('CNAME');
                $ci = db('zhcity')->where('id',$row['KP_Citys'])->cache()->value('CNAME');
                $di = db('zhcity')->where('id',$row['KP_District'])->cache()->value('CNAME');
                $row['area_text'] = $pr.'/'.$ci.'/'.$di;
            }
        }else{
            $pr = db('zhcity')->where('id',$row['KP_Provice'])->cache()->value('CNAME');
            $ci = db('zhcity')->where('id',$row['KP_Citys'])->cache()->value('CNAME');
            $di = db('zhcity')->where('id',$row['KP_District'])->cache()->value('CNAME');
            $co = db('zhcity')->where('id',$row['KP_County'])->cache()->value('CNAME');
            $row['area_text'] = $pr.'/'.$ci.'/'.$di.'/'.$co;
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
}
