<?php

namespace app\admin\controller\general;

use app\admin\model\Admin;
use app\common\controller\Backend;
use fast\Random;
use think\Session;

/**
 * 个人配置
 *
 * @icon fa fa-user
 */
class Profile extends Backend
{
    protected $noNeedRight = ['update','pass','email','phone'];
    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            $model = model('AdminLog');
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $model
                    ->where($where)
                    ->where('admin_id', $this->auth->id)
                    ->order($sort, $order)
                    ->count();

            $list = $model
                    ->where($where)
                    ->where('admin_id', $this->auth->id)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        $info = Admin::get($this->auth->id);
        $info['Bm_text'] = db('bm')->where('ID',$info['KP_Bmid'])->value('KP_BmName');
        $info['zw_text'] = db('zw')->where('id',$info['KP_Zhiwu'])->value('KP_ZwName');;
        $this->view->assign('info',$info);
        return $this->view->fetch();
    }

    /**
     * 更新个人信息
     */
    public function update()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            $params = array_filter(array_intersect_key($params, array_flip(array('email', 'nickname', 'password', 'avatar'))));
            unset($v);
            if (isset($params['password']))
            {
                $params['salt'] = Random::alnum();
                $params['password'] = md5(md5($params['password']) . $params['salt']);
            }
            if ($params)
            {
                $admin = Admin::get($this->auth->id);
                $admin->save($params);
                //因为个人资料面板读取的Session显示，修改自己资料后同时更新Session
                Session::set("admin", $admin->toArray());
                $this->success();
            }
            $this->error();
        }
        return;
    }

    /**
     * 更新邮箱
     */
    public function email()
    {
        /*if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            if (isset($params['password']))
            {
                $params['salt'] = Random::alnum();
                $params['password'] = md5(md5($params['password']) . $params['salt']);
            }
            if ($params)
            {
                $admin = Admin::get($this->auth->id);
                $admin->save($params);
                //因为个人资料面板读取的Session显示，修改自己资料后同时更新Session
                Session::set("admin", $admin->toArray());
                $this->success();
            }
            $this->error();
        }*/
        return;
    }

    /**
     * 更新手机
     */
    public function phone()
    {
        /*if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            $params = array_filter(array_intersect_key($params, array_flip(array('email', 'nickname', 'password', 'avatar'))));
            unset($v);
            if (isset($params['password']))
            {
                $params['salt'] = Random::alnum();
                $params['password'] = md5(md5($params['password']) . $params['salt']);
            }
            if ($params)
            {
                $admin = Admin::get($this->auth->id);
                $admin->save($params);
                //因为个人资料面板读取的Session显示，修改自己资料后同时更新Session
                Session::set("admin", $admin->toArray());
                $this->success();
            }
            $this->error();
        }*/
        return;
    }
    //获取邮箱验证码
    /*public getCode(){

    }*/

    /**
     * 更新个人信息
     */
    public function pass()
    {
        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");
            //$params = array_filter(array_intersect_key($params, array_flip(array('email', 'nickname', 'password', 'avatar'))));
           // unset($v);
            $pass = md5(md5($params['oldpass']) . $this->auth->salt);
            $row = Admin::get(['nickname'=>Session::get('admin.nickname'),'password'=>$pass]);
            if (!$row) {
                $this->error('原密码错误');
            }
            if ($params['newpass']!=$params['surepass']) {
                 $this->error('密码不一致');
            }
            if (isset($params['newpass']))
            {
                $list['salt'] = Random::alnum();
                $list['password'] = md5(md5($params['newpass']) . $list['salt']);
            }
            if ($list)
            {
                $admin = Admin::get($this->auth->id);
                $admin->save($list);
                //因为个人资料面板读取的Session显示，修改自己资料后同时更新Session
                Session::set("admin", $admin->toArray());
                $this->success();
            }
            $this->error();
        }
        return;
    }

}
