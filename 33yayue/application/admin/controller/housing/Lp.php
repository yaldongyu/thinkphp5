<?php

namespace app\admin\controller\housing;

use app\common\controller\Backend;
use app\admin\model\Lpht as LpModel;
use fast\Pinyin;
use think\Session;
use think\Db;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Lp extends Backend
{
    
    /**
     * Lp模型对象
     * @var \app\admin\model\Lp
     */
    protected $model = null;
    protected $searchFields = 'id,KP_LpName';
    protected $relationSearch = true;
    protected $noNeedRight = ['selectpage','pinyin'];
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Lpht;
        $this->view->assign("typeList", LpModel::getTypeList());
    } 
    

     public function index()
    {
        //设置过滤方法
        /*set_time_limit(0);
        $all = $this->model->order('id desc')->limit(67)->select();
        foreach ($all as $key => $value) {
            if ($value['KP_City']) {
                $key = $value['KP_City'];
                $keypid = -1;
                $str = '';
                while($keypid !=0) {
                  $arr = db('zhcity')->where('id',$key)->find();
                  if ($arr['CLEVEL']==1) {
                     $this->model->update(['KP_Provice' => $arr['id'],'id'=>$value['id']]);
                  }
                  if ($arr['CLEVEL']==2) {
                     $this->model->update(['KP_Citys' => $arr['id'],'id'=>$value['id']]);
                  }
                  if ($arr['CLEVEL']==3) {
                     $this->model->update(['KP_District' => $arr['id'],'id'=>$value['id']]);
                  }
                  if ($arr['CLEVEL']==4) {
                     $this->model->update(['KP_County' => $arr['id'],'id'=>$value['id']]);
                  }
                  
                  $key = $arr['CUPID'];
                  $keypid = $arr['CUPID'];
                } 
            }
        }
        echo '完成';*/
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            $jg = $this->request->get("price", '');
            $cityid = $this->request->get("kpcity", 0);
            if ($cityid) {
                $citywhere = " KP_Provice=".$cityid." or KP_Citys=".$cityid." or KP_District=".$cityid." or KP_County=".$cityid;
            }else{
                $citywhere = '1=1';
            }
            if ($jg==2||$jg==3||$jg==4) {
                $str = 'lpprice.KP_Qiprice';
                if ($jg==2) {
                    $str = 'lpprice.KP_Qiprice';
                }elseif($jg==3){
                    $str = 'lpprice.KP_Juprice';
                }
                $obj = $this->request->get("rprice", '');
                list($where, $sort, $order, $offset, $limit) = $this->buildparams();
                $total = $this->model
                ->with(['lpprice'])
                ->where($str,'>=',$obj)
                ->where($where)
                ->where($citywhere)
                ->order($sort, $order)
                ->group('lpprice.Kp_LpID')
                ->count();

                $list = $this->model
                ->with(['lpprice'])
                ->where($where)
                ->where($citywhere)
                ->where($str,'>=',$obj)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->group('lpprice.Kp_LpID')
                ->select();
            }else{
                list($where, $sort, $order, $offset, $limit) = $this->buildparams(null,false);
                $total = $this->model
                ->where($where)
                ->where($citywhere)
                ->order($sort, $order)
                ->count();

                $list = $this->model
                ->where($where)
                ->where($citywhere)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
                $this->model->getlastsql();
            }
            foreach ($list as $key => $value) {
                $value['KP_CityName'] = db('zhcity')->where('ID',$value['KP_City'])->value('CNAME');
                $value['KP_Qiprice'] = db('lpprice')->where('Kp_LpID',$value['id'])->order('id desc')->value('KP_Qiprice');
                $value['KP_Juprice'] = db('lpprice')->where('Kp_LpID',$value['id'])->order('id desc')->value('KP_Juprice');
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
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
                    //$params['KP_Hx'] = implode(',', $params['KP_Hx']);
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
        //$hxdata = Array (1=>'一居',2=>'二居' ,3=>'三居', 4=>'四居',  5=>'五居',  6=>'其他' );
        //$this->view->assign("hxdata",$hxdata);
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
                   // $params['KP_Hx'] = implode(',', $params['KP_Hx']);
                    $params['KP_EditUser'] = Session::get('admin.nickname');
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
        $row['KP_City_text'] = '';
        if ($row['KP_City']) {
            $key = $row['KP_City'];
            $keypid = -1;
            $str = '';
            while($keypid !=0) {
              $arr = db('zhcity')->where('id',$key)->find();
              if ($str=='') {
                  $str = $arr['CNAME'];
              }else{
                  $str = $arr['CNAME'].'/'.$str;
              }
              
              $key = $arr['CUPID'];
              $keypid = $arr['CUPID'];
            } 
            $row['KP_City_text'] =$str;
        }
        $row['KP_Jzlx'] = str_replace(' ',',',$row['KP_Jzlx']);
        //$hxdata = Array (1=>'一居',2=>'二居' ,3=>'三居', 4=>'四居',  5=>'五居',  6=>'其他' );
        $this->view->assign("row", $row);
       // $this->view->assign("hxdata",$hxdata);
        return $this->view->fetch();
    }


    public function pinyin(){
         $params = $this->request->post();
         $qp = Pinyin::get($params['str']);
         $jp = Pinyin::get($params['str'],true);

         return json(array("quanpin" => $qp, "jianpin" =>$jp));  
    }

    public function selectpage()
    {
        return parent::selectpage();
    }
}
