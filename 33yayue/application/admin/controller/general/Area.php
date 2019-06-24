<?php

namespace app\admin\controller\general;

use app\common\controller\Backend;
use think\Session;
use fast\Pinyin;
use think\Cache;
use fast\Tree;
use think\Db;
/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Area extends Backend
{
    
    /**
     * Area模型对象
     * @var \app\admin\model\Area
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Area;
        
    } 

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
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model

                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->field('id as aid,areaid as id ,areapid as pid ,name as title,domain,weigh,status,ishot,provjuprice,curjuprice')
                ->where($where)
                ->order($sort, $order)
                ->select();

            foreach ($list as $key => $value) {
                $list[$key]['domain_text'] = $value['domain'];
            }
            $lists = collection($list)->toArray();
            Tree::instance()->init($lists);
            $lists = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');
            //print_r($this->rulelist);exit;
           // $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $lists);

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
                    $params['adduser'] = Session::get('admin.nickname');
                    $params['addtime'] = date("Y-m-d H:i:s");
                    $params['edituser'] = Session::get('admin.nickname');
                    $params['edittime'] = date("Y-m-d H:i:s");
                    $result = $this->model->allowField(true)->save($params);
                    if ($result !== false) {
                        Cache::clear();
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
                        Cache::clear();
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
        $row['KP_Area_text'] = '';
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
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids) {
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $count = $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list = $this->model->where($pk, 'in', $ids)->select();
            $count = 0;
            foreach ($list as $k => $v) {
                $count += $v->delete();
            }
            if ($count) {
                Cache::clear();
                $this->success();
            } else {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 真实删除
     */
    public function destroy($ids = "")
    {
        $pk = $this->model->getPk();
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $count = $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        if ($ids) {
            $this->model->where($pk, 'in', $ids);
        }
        $count = 0;
        $list = $this->model->onlyTrashed()->select();
        foreach ($list as $k => $v) {
            $count += $v->delete(true);
        }
        if ($count) {
            Cache::clear();
            $this->success();
        } else {
            $this->error(__('No rows were deleted'));
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 批量更新
     */
    public function multi($ids = "")
    {
        $ids = $ids ? $ids : $this->request->param("ids");
        if ($ids) {
            if ($this->request->has('params')) {
                parse_str($this->request->post("params"), $values);
                if (!$this->auth->isSuperAdmin()) {
                    $values = array_intersect_key($values, array_flip(is_array($this->multiFields) ? $this->multiFields : explode(',', $this->multiFields)));
                }
                if ($values) {
                    $adminIds = $this->getDataLimitAdminIds();
                    if (is_array($adminIds)) {
                        $this->model->where($this->dataLimitField, 'in', $adminIds);
                    }
                    $count = 0;
                    $list = $this->model->where($this->model->getPk(), 'in', $ids)->select();
                    foreach ($list as $index => $item) {
                        $count += $item->allowField(true)->isUpdate(true)->save($values);
                    }
                    if ($count) {
                        Cache::clear();
                        $this->success();
                    } else {
                        $this->error(__('No rows were updated'));
                    }
                } else {
                    $this->error(__('You have no permission'));
                }
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    public function pinyin(){
         $domain = $_SERVER['HTTP_HOST'];
         $fir = explode('.', $domain);
         $params = $this->request->post();
         $qp = Pinyin::get($params['str']);
         return json(array("quanpin" => $qp.".".$fir[1].".".$fir[2]));  
    }


    public function weigh()
    {
        //排序的数组

        $ids = $this->request->post("ids");
        //拖动的记录ID
        $changeid = $this->request->post("changeid");
        //操作字段
        $field = $this->request->post("field");
        //操作的数据表
        $table = $this->request->post("table");
        //排序的方式
        $orderway = $this->request->post("orderway", "", 'strtolower');
        $orderway = $orderway == 'asc' ? 'ASC' : 'DESC';
        $sour = $weighdata = [];
        $ids = explode(',', $ids);
        $prikey = 'id';
        $pid ='';
        //限制更新的字段
        $field = in_array($field, ['weigh']) ? $field : 'weigh';

        // 如果设定了pid的值,此时只匹配满足条件的ID,其它忽略
        if ($pid !== '') {
            $hasids = [];
            $list = Db::name($table)->where($prikey, 'in', $ids)->where('pid', 'in', $pid)->field('id,pid')->select();
            foreach ($list as $k => $v) {
                $hasids[] = $v['id'];
            }
            $ids = array_values(array_intersect($ids, $hasids));
        }

        $list = Db::name($table)->field("$prikey,$field")->where($prikey, 'in', $ids)->order($field, $orderway)->select();
        foreach ($list as $k => $v) {
            $sour[] = $v[$prikey];
            $weighdata[$v[$prikey]] = $v[$field];
        }
        $position = array_search($changeid, $ids);
        $desc_id = $sour[$position];    //移动到目标的ID值,取出所处改变前位置的值
        $sour_id = $changeid;
        $weighids = array();
        $temp = array_values(array_diff_assoc($ids, $sour));
        foreach ($temp as $m => $n) {
            if ($n == $sour_id) {
                $offset = $desc_id;
            } else {
                if ($sour_id == $temp[0]) {
                    $offset = isset($temp[$m + 1]) ? $temp[$m + 1] : $sour_id;
                } else {
                    $offset = isset($temp[$m - 1]) ? $temp[$m - 1] : $sour_id;
                }
            }
            $weighids[$n] = $weighdata[$offset];
            Db::name($table)->where($prikey, $n)->update([$field => $weighdata[$offset]]);
        }
        Cache::clear();
        $this->success();
    }
    

}
