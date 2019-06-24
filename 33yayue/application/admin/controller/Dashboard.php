<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;
use app\admin\model\AuthGroupAccess as AuthGroupAccessModel;
use app\admin\model\Admin as AdminModel;
use app\admin\model\Lp as LpModel;
use app\admin\model\News as NewsModel;
use app\admin\model\Lppic as LppicModel;
use app\admin\model\Lphxpic as LphxpicModel;
/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{   
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    /**
     * 查看
     */
    public function index()
    {
        $seventtime = \fast\Date::unixtime('day', -7);
        $paylist = $createlist = [];
        for ($i = 0; $i < 7; $i++) 
        {
            $day = date("Y-m-d", $seventtime + ($i * 86400));
            $createlist[$day] = mt_rand(20, 200);
            $paylist[$day] = mt_rand(1, mt_rand(1, $createlist[$day]));
        }
        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
        Config::parse($addonComposerCfg, "json", "composer");
        $config = Config::get("composer");
        $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');

        $AuthGroupAccessModel = new AuthGroupAccessModel;
        $uid = $AuthGroupAccessModel->field('uid')->where('group_id','in',[1,8])->select();
        $uidarr = array();
        foreach ($uid as $key => $value) {
            $uidarr[] = $value['uid'];
        }
        $AdminModel = new AdminModel;
        $uname = $AdminModel->field('nickname')->where('id','in',$uidarr)->where('KP_Bmid',1)->select();
        $unamearr = array();
        foreach ($uname as $key => $value) {
            $unamearr[] = $value['nickname'];
        }
        $LpModel = new LpModel;
        $NewsModel = new NewsModel;
        $Lppic = new LppicModel;
        $Lphxpic = new LphxpicModel;
        /*$yeji = $LpModel->field('count(KP_AddUser) as num,KP_AddUser')->where('KP_AddUser','in',$unamearr)->where('KP_AddTime','like','%'.date("Y-m-d",strtotime("today")))->group('KP_AddUser')->select();*/
        $where = "KP_AddTime > '".date("Y-m-d",strtotime("-30 day"))."' and KP_AddTime < '".date("Y-m-d",strtotime("today"))."'";
        $yeji = $LpModel->field('count(*) as num,KP_AddUser')->where('KP_AddUser','in',$unamearr)->where($where)->group('KP_AddUser')->select();
        $zxyeji = $NewsModel->field('count(*) as num,KP_AddUser')->where('KP_AddUser','in',$unamearr)->where($where)->group('KP_AddUser')->select();
        $xcyeji = $Lppic->field('count(*) as num,KP_AddUser')->where('KP_AddUser','in',$unamearr)->where($where)->group('KP_AddUser')->select();
        $hxyeji = $Lphxpic->field('count(*) as num,KP_AddUser')->where('KP_AddUser','in',$unamearr)->where($where)->group('KP_AddUser')->select();
        $namearr = array();
        $numarr = array();
        $nums = array();
        $zxnums = array();
        $zxnamearr = array();
        $zxnumarr = array();
        $xcnumarr = array();
        $hxnumarr = array();
        $picnumarr = array();
        /*foreach ($yeji as $key => $value) {
            $namearr[] = $value['KP_AddUser'];
            $numarr[] = $value['num'];
        }*/
        foreach ($yeji as $key => $value) {
            $nums[$value['KP_AddUser']] = $value['num'];
        }
        foreach ($zxyeji as $key => $value) {
            $zxnums[$value['KP_AddUser']] = $value['num'];
        }
        foreach ($xcyeji as $key => $value) {
            $xcnumarr[$value['KP_AddUser']] = $value['num'];
        }
        foreach ($hxyeji as $key => $value) {
            $hxnumarr[$value['KP_AddUser']] = $value['num'];
        }
        foreach ($unamearr as $key => $value) {     
            $f1 = isset($nums[$value])?$nums[$value]:0;
            $f2 = isset($zxnums[$value])?$zxnums[$value]:0;
            $f3 = isset($xcnumarr[$value])?$xcnumarr[$value]:0;
            $f4 = isset($hxnumarr[$value])?$hxnumarr[$value]:0;
            $f5 = $f3+$f4;
            if ($f1!=0||$f2!=0||$f5!=0) {
                $numarr[] = $f1;
                $zxnumarr[] = $f2;
                $picnumarr[] = $f5;
                $namearr[] = $value;
            }
        }
/*        $NewsModel = new NewsModel;
        $zxyeji = $NewsModel->field('count(*) as num,KP_AddUser')->where('KP_AddUser','in',$unamearr)->group('KP_AddUser')->select();
        $zxnamearr = array();
        $zxnumarr = array();
        foreach ($zxyeji as $key => $value) {
            $zxnamearr[] = $value['KP_AddUser'];
            $zxnumarr[] = $value['num'];
        }*/

        $this->view->assign([
            'totaluser'        => 35200,
            'totalviews'       => 219390,
            'totalorder'       => 32143,
            'totalorderamount' => 174800,
            'todayuserlogin'   => 321,
            'todayusersignup'  => 430,
            'todayorder'       => 2324,
            'unsettleorder'    => 132,
            'sevendnu'         => '80%',
            'sevendau'         => '32%',
            'paylist'          => $paylist,
            'createlist'       => $createlist,
            'addonversion'       => $addonVersion,
            'uploadmode'       => $uploadmode,
            'namearr'       => $namearr,
            'numarr'       => $numarr,
            'zxnumarr'       => $zxnumarr,
            'picnumarr'       => $picnumarr
        ]);

        return $this->view->fetch();
    }

    public function getData(){
        $params = $this->request->post();
        if ($params['position']==1) {
            $where = "KP_AddTime like '".date("Y-m-d",strtotime("today"))."%'";
        }elseif($params['position']==2){
            $where = "KP_AddTime > '".date("Y-m-d",strtotime("-7 day"))."' and KP_AddTime < '".date("Y-m-d",strtotime("today"))."'";
        }elseif($params['position']==3){
            $where = "KP_AddTime > '".date("Y-m-d",strtotime("-30 day"))."' and KP_AddTime < '".date("Y-m-d",strtotime("today"))."'";
        }else{
            $where='1=1';
        }
        $AuthGroupAccessModel = new AuthGroupAccessModel;
        $uid = $AuthGroupAccessModel->field('uid')->where('group_id','in',[1,8])->select();
        $uidarr = array();
        foreach ($uid as $key => $value) {
            $uidarr[] = $value['uid'];
        }
        $AdminModel = new AdminModel;
        $uname = $AdminModel->field('nickname')->where('id','in',$uidarr)->where('KP_Bmid',1)->select();
        $unamearr = array();
        foreach ($uname as $key => $value) {
            $unamearr[] = $value['nickname'];
        }
        $LpModel = new LpModel;
        $NewsModel = new NewsModel;
        $Lppic = new LppicModel;
        $Lphxpic = new LphxpicModel;
        /*$yeji = $LpModel->field('count(KP_AddUser) as num,KP_AddUser')->where('KP_AddUser','in',$unamearr)->where('KP_AddTime','like','%'.date("Y-m-d",strtotime("today")))->group('KP_AddUser')->select();*/
        $yeji = $LpModel->field('count(*) as num,KP_AddUser')->where('KP_AddUser','in',$unamearr)->where($where)->group('KP_AddUser')->select();
        $zxyeji = $NewsModel->field('count(*) as num,KP_AddUser')->where('KP_AddUser','in',$unamearr)->where($where)->group('KP_AddUser')->select();
        $xcyeji = $Lppic->field('count(*) as num,KP_AddUser')->where('KP_AddUser','in',$unamearr)->where($where)->group('KP_AddUser')->select();
        $hxyeji = $Lphxpic->field('count(*) as num,KP_AddUser')->where('KP_AddUser','in',$unamearr)->where($where)->group('KP_AddUser')->select();
        $namearr = array();
        $numarr = array();
        $nums = array();
        $zxnums = array();
        $zxnamearr = array();
        $zxnumarr = array();
        $xcnumarr = array();
        $hxnumarr = array();
        $picnumarr = array();
        /*foreach ($yeji as $key => $value) {
            $namearr[] = $value['KP_AddUser'];
            $numarr[] = $value['num'];
        }*/
        foreach ($yeji as $key => $value) {
            $nums[$value['KP_AddUser']] = $value['num'];
        }
        foreach ($zxyeji as $key => $value) {
            $zxnums[$value['KP_AddUser']] = $value['num'];
        }
        foreach ($xcyeji as $key => $value) {
            $xcnumarr[$value['KP_AddUser']] = $value['num'];
        }
        foreach ($hxyeji as $key => $value) {
            $hxnumarr[$value['KP_AddUser']] = $value['num'];
        }
        foreach ($unamearr as $key => $value) {     
            $f1 = isset($nums[$value])?$nums[$value]:0;
            $f2 = isset($zxnums[$value])?$zxnums[$value]:0;
            $f3 = isset($xcnumarr[$value])?$xcnumarr[$value]:0;
            $f4 = isset($hxnumarr[$value])?$hxnumarr[$value]:0;
            $f5 = $f3+$f4;
            if ($f1!=0||$f2!=0||$f5!=0) {
                $numarr[] = $f1;
                $zxnumarr[] = $f2;
                $picnumarr[] = $f5;
                $namearr[] = $value;
            }
        }
        $index['names'] = $namearr;
        $index['nums'] = $numarr;
        $index['zxnums'] = $zxnumarr;
        $index['picnums'] = $picnumarr;
        return json(collection($index)->toArray());
    }

}
