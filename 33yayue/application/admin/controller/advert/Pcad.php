<?php

namespace app\admin\controller\advert;

use app\common\controller\Backend;
use app\common\model\SiteName as SitenameModel;
 use think\Session;
/**
 * PC广告设置
 *
 * @icon fa fa-circle-o
 */
class Pcad extends Backend
{
    
    /**
     * Pcgeneralad模型对象
     * @var \app\admin\model\Pcgeneralad
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize(); 
        $this->model = new \app\admin\model\Pcgeneralad;
        $this->view->assign("typeList", config('site.Zhandian'));
       
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
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            $cityid = $this->request->get("kpcity", 0);
            if ($cityid) {
                $citywhere = " KP_Provice=".$cityid." or KP_Citys=".$cityid." or KP_District=".$cityid." or KP_County=".$cityid;
            }else{
                $citywhere = '1=1';
            }
            $kptype=$this->request->request('KP_Type');
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
          
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

            $list = collection($list)->toArray();
            //$dzarr = SitenameModel::getTypeList();
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
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
          $this->view->assign("typeList",SitenameModel::getTypeList());
      
        return $this->view->fetch();
    }

    /*public function changeweigh(){
        $arr = $this->model->order('id asc')->select();
        foreach ($arr as $key => $value) {
            $this->model->where('id',$value['id'])->setField('weigh', ($key+1));
        }
    }*/
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
