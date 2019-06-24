<?php

namespace app\admin\controller\auth;

use app\common\controller\Backend;
use app\admin\model\AuthGroup;
use app\admin\model\Bm;
use app\admin\model\AuthGroupAccess;
use fast\Random;
use fast\Tree;
/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Admin extends Backend
{
    
    /**
     * Admin模型对象
     * @var \app\admin\model\Admin
     */
    protected $model = null;
    protected $childrenGroupIds = [];
    protected $childrenAdminIds = [];
    protected $searchFields = 'id,username,nickname';
    protected $noNeedRight = ['roletree'];
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Admin');

        $this->childrenAdminIds = $this->auth->getChildrenAdminIds(true);
        $this->childrenGroupIds = $this->auth->getChildrenGroupIds(true);

        $groupList = collection(AuthGroup::where('id', 'in', $this->childrenGroupIds)->select())->toArray();

        Tree::instance()->init($groupList);
        $groupdata = [];
        if ($this->auth->isSuperAdmin())
        {
            $result = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0));
            foreach ($result as $k => $v)
            {
                $groupdata[$v['id']] = $v['name'];
            }
        }
        else
        {
            $result = [];
            $groups = $this->auth->getGroups();
            foreach ($groups as $m => $n)
            {
                $childlist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray($n['id']));
                $temp = [];
                foreach ($childlist as $k => $v)
                {
                    $temp[$v['id']] = $v['name'];
                }
                $result[__($n['name'])] = $temp;
            }
            $groupdata = $result;
        }

        // 必须将结果集转换为数组
        $ruleList = collection(Bm::field("ID as id,KP_Upid as pid,KP_BmName as name")->order('KP_px', 'Desc')->select())->toArray();
        Tree::instance()->init($ruleList);
        $list = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'name');
        foreach ($list as $k => &$v)
        {
            $ruledata[$v['id']] = $v['name'];
        }
        $this->view->assign('ruledata', $ruledata);
        $this->view->assign('groupdata', $groupdata);
        $this->assignconfig("admin", ['id' => $this->auth->id]);
    }
    

     /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            $childrenGroupIds = $this->childrenGroupIds;
            $groupName = AuthGroup::where('id', 'in', $childrenGroupIds)
                    ->column('id,name');
            $authGroupList = AuthGroupAccess::where('group_id', 'in', $childrenGroupIds)
                    ->field('uid,group_id')
                    ->select();

            $adminGroupName = [];
            foreach ($authGroupList as $k => $v)
            {
                if (isset($groupName[$v['group_id']]))
                    $adminGroupName[$v['uid']][$v['group_id']] = $groupName[$v['group_id']];
            }
            $groups = $this->auth->getGroups();
            foreach ($groups as $m => $n)
            {
                $adminGroupName[$this->auth->id][$n['id']] = $n['name'];
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->where($where)
                    ->where('id', 'in', $this->childrenAdminIds)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->where($where)
                    ->where('id', 'in', $this->childrenAdminIds)
                    ->field(['password', 'salt', 'token'], true)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            foreach ($list as $k => &$v)
            {
                $groups = isset($adminGroupName[$v['id']]) ? $adminGroupName[$v['id']] : [];
                $v['groups'] = implode(',', array_keys($groups));
                $v['groups_text'] = implode(',', array_values($groups));
                $list[$k]['Bm_text'] = db('bm')->where('ID',$v['KP_Bmid'])->value('KP_BmName');
                $list[$k]['zw_text'] = db('zw')->where('id',$v['KP_Zhiwu'])->value('KP_ZwName');;
            }
            unset($v);
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
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                $params['username'] = $params['nickname'];                
                $params['salt'] = Random::alnum();
                $params['password'] = md5(md5($params['password']) . $params['salt']);
               // echo md5(md5($password) . $params['salt']).'--'.$params['salt']."--".$password;exit;
                $params['avatar'] = $params['avatar']?$params['avatar']:'/assets/img/avatar.png'; //设置新管理员默认头像。
                $loginid = $this->model->max('KP_Loginid');
                if ($loginid<1) {
                    $params['KP_Loginid'] = 10001;
                }else{
                    $params['KP_Loginid'] = $loginid+1;
                }
                $result = $this->model->validate('Admin.add')->save($params);
                if ($result === false)
                {
                    $this->error($this->model->getError());
                }
                $group = $this->request->post("group/a");

                //过滤不允许的组别,避免越权
                $group = array_intersect($this->childrenGroupIds, $group);
                $dataset = [];
                foreach ($group as $value)
                {
                    $dataset[] = ['uid' => $this->model->id, 'group_id' => $value];
                }
                model('AuthGroupAccess')->saveAll($dataset);
                $this->success();
            }
            $this->error();
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row)
            $this->error(__('No Results were found'));
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if ($params)
            {
                $params['username'] = $params['nickname'];  
                if ($params['password'])
                {
                    $params['salt'] = Random::alnum();
                    $params['password'] = md5(md5($params['password']) . $params['salt']);
                }
                else
                {
                    unset($params['password'], $params['salt']);
                }
                //这里需要针对username和email做唯一验证
                $adminValidate = \think\Loader::validate('Admin');
                $adminValidate->rule([
                    'username' => 'require|max:50|unique:admin,username,' . $row->id,
                    'email'    => 'require|email|unique:admin,email,' . $row->id
                ]);
                $result = $row->validate('Admin.edit')->save($params);
                if ($result === false)
                {
                    $this->error($row->getError());
                }

                // 先移除所有权限
                model('AuthGroupAccess')->where('uid', $row->id)->delete();

                $group = $this->request->post("group/a");

                // 过滤不允许的组别,避免越权
                $group = array_intersect($this->childrenGroupIds, $group);

                $dataset = [];
                foreach ($group as $value)
                {
                    $dataset[] = ['uid' => $row->id, 'group_id' => $value];
                }
                model('AuthGroupAccess')->saveAll($dataset);
                $this->success();
            }
            $this->error();
        }
        $grouplist = $this->auth->getGroups($row['id']);
        $groupids = [];
        foreach ($grouplist as $k => $v)
        {
            $groupids[] = $v['id'];
        }
        $this->view->assign("row", $row);
        $this->view->assign("groupids", $groupids);
        return $this->view->fetch();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids)
        {
            // 避免越权删除管理员
            $childrenGroupIds = $this->childrenGroupIds;
            $adminList = $this->model->where('id', 'in', $ids)->where('id', 'in', function($query) use($childrenGroupIds) {
                        $query->name('auth_group_access')->where('group_id', 'in', $childrenGroupIds)->field('uid');
                    })->select();
            if ($adminList)
            {
                $deleteIds = [];
                foreach ($adminList as $k => $v)
                {
                    $deleteIds[] = $v->id;
                }
                $deleteIds = array_diff($deleteIds, [$this->auth->id]);
                if ($deleteIds)
                {
                    $this->model->destroy($deleteIds);
                    model('AuthGroupAccess')->where('uid', 'in', $deleteIds)->delete();
                    $this->success();
                }
            }
        }
        $this->error();
    }


    public function roletree()
    {
        $model = model('bm');
        $nodeList = $model->field("id,KP_Upid as pid,KP_BmName")->select();
        if ($nodeList) {
             foreach ($nodeList as $k => $v)
                {
                    $state = array('selected' =>true);
                    $nodeLists[] = array('id' => $v['id'], 'parent' => $v['pid'] ? $v['pid'] : '#', 'text' => __($v['KP_BmName']), 'type' => 'menu', 'state' => $state);
                }
             $this->success('', null, $nodeLists);
        }
        else
        {
            $this->error(__('Group not found'));
        }
    }

}
