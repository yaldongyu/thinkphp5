<?php

namespace app\admin\controller\groudbuy;

use app\common\controller\Backend;
use app\admin\model\Lptg as LptgModel;
use app\admin\model\Lp as LpModel;
use think\Session;
use think\Db;

/**
 * 房产团购
 *
 * @icon fa fa-circle-o
 */
class Lptg extends Backend
{
    
    /**
     * Lptg模型对象
     * @var \app\admin\model\Lptg
     */
    protected $model = null;
    protected $multiFields='KP_Htzt';
    protected $searchFields = 'KP_Title,lp.KP_LpName';
   
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Lptg;
       // $this->modelsite = new \app\admin\model\Lptg;
         $this->view->assign("typeList", LptgModel::getTypeList());
          $this->view->assign("lptgztList", LptgModel::getLptgztList());
         // $this->view->assign("LpNamelist", LpModel::select());

    }
   
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
         
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            $cityid = $this->request->get("kpcity", 0);
            if ($cityid) {
                $citywhere = " lptg.KP_Provice=".$cityid." or lptg.KP_Citys=".$cityid." or lptg.KP_District=".$cityid." or lptg.KP_County=".$cityid;
            }else{
                $citywhere = '1=1';
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with(['lp'])
                    ->where($where)
                    ->where($citywhere)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['lp'])
                    ->where($where)
                    ->where($citywhere)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
                
            foreach ($list as $key => $value) {
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

                    $params['KP_AddUser'] = Session::get('admin.nickname');
                    $params['KP_AddTime'] = date("Y-m-d H:i:s");
                    $params['KP_EditUser'] = Session::get('admin.nickname');
                    $params['KP_EditTime'] = date("Y-m-d H:i:s");

                    $result = $this->model->allowField(true)->save($params);


                    //$result = $this->model->allowField(true)->save($params);
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
        $weigh = $this->model->max('weigh');
        $this->view->assign("weigh",$weigh+1);
        return $this->view->fetch();
    }






     /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
         // $row = $this->model
         //            ->with(['lp'])
         //            ->where('lptg.id',$ids)                    
         //            ->select();
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

                     $params['KP_AddUser'] = Session::get('admin.nickname');
                    $params['KP_AddTime'] = date("Y-m-d H:i:s");
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

       // lp__KP_LpName KP_LpID $this->model->get($ids);
        $LpID =   $row['KP_LpID'] ;
        $KP_LpName =  LpModel::where('id',$LpID)->value('KP_LpName');
         $row['lp__KP_LpName']= $KP_LpName ;
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

    public function getCity(){
        $params = $this->request->post();
        $LpModel = new LpModel;
        return $LpModel->field('KP_Provice,KP_Citys,KP_District,KP_County')->where('id',$params['id'])->find();
    }

}
