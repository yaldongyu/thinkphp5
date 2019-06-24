<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\common\library\Token;
use app\admin\model\Lp as LpModel;
use app\admin\model\Lpcomment as LpcommentModel;
use app\admin\model\Gflmkf as GflmkfModel;
use app\admin\model\Unioncomment as UnioncommentModel;
use app\admin\model\News as NewsModel;

use think\Db;
class Houseunion extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $LpModel = new LpModel;
        //联盟楼盘
         $lpmodel = $LpModel
                        ->field('id,KP_Wjt,KP_LpName,KP_Gflm,KP_City,KP_Tel,KP_Fjh,KP_Xszt,KP_Wylx,KP_TsType,KP_Lpdz,KP_YouHui,KP_TaoJia,KP_Cs,KP_Zycs,KP_Jz')
                        ->where('KP_Provice|KP_Citys|KP_District|KP_County',$this->City)
                        ->where('KP_Gflm',1)
                        ->limit(6)
                        ->order('KP_Top desc,KP_EditTime desc')
                        ->buildSql();
        $index['lmlp'] = $LpModel->table($lpmodel)->alias('a')
                        ->join('lpprice b','b.KP_LpID=a.id','left')
                        ->field('a.id,a.KP_Wjt,a.KP_LpName,a.KP_Gflm,a.KP_City,a.KP_Tel,a.KP_Fjh,a.KP_Xszt,a.KP_Wylx,a.KP_TsType,a.KP_Lpdz,a.KP_YouHui,a.KP_TaoJia,
                                 b.KP_Qiprice,b.KP_Juprice,a.KP_Cs,a.KP_Zycs')
                        ->limit(6)->select();
        $arr = array();
        foreach ($index['lmlp'] as $key => $value) {
            $arr[] = $value['id'];           
        }

        $LpcommentModel = new LpcommentModel;
        $lpcommentnum = $LpcommentModel->field('count(KP_LpID) as num,KP_LpID')
                                        ->where('KP_Check',1)
                                        ->where('KP_LpID','in',$arr)
                                        ->group('KP_LpID')->select();  
        foreach ($lpcommentnum as $k => $v) {
            $dpnum[$v['KP_LpID']][] = $v;
        }                                 
        foreach ($index['lmlp'] as $k => $v) {
            $v['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAME');
            $v['KP_Qiprice'] = floatval($v['KP_Qiprice']);
            $v['KP_Juprice'] = floatval($v['KP_Juprice']);
            $v['dpnum'] = isset($dpnum[$v['id']])?$dpnum[$v['id']][0]['num']:0;
        }

        //购房联盟客服
        $GflmkfModel = new GflmkfModel;
        $index['gflmkf'] = $GflmkfModel->field('id,KP_Name,KP_Tel,KP_Area,KP_Pic,KP_Qy')->select();

        //联盟动态
        $NewsModel = new NewsModel;
        $index['lmdt'] = $NewsModel
                           ->field('id,KP_Title')
                           ->where("KP_Provice|KP_Citys|KP_District|KP_County",$this->City)
                           ->where('KP_Lmid',14)
                           ->order('id desc')->limit(9)
                           ->select();

        //咨询列表
        $UnioncommentModel = new UnioncommentModel;
        $index['list'] = $UnioncommentModel->field("id,KP_KhName,KP_KhPhone,KP_Content,KP_Time,KP_Reply,KP_ServiceName,KP_ServiceTel
                                                    ,KP_FlagHf,KP_Check")
                                             ->where(["KP_Check"=>1])
                                             ->limit(6)
                                             ->order("id desc")->select();                   
         $index['total'] = $UnioncommentModel->field("id")
                                             ->where(["KP_Check"=>1])
                                             ->order("id desc")->count("id");                                    
        //print_r(collection($index['lmdt'])->toArray()); exit;   
        $this->view->assign("index",$index);                     
        return $this->view->fetch("index/houseunion");

    }

    public function commentpage(){
        $params = $this->request->post();
        $page = isset($params['page'])?$params['page']:0;
        $pagesize = isset($params['pagesize'])?$params['pagesize']:6;
        $pages = ($page-1)*$pagesize;
        //咨询列表
        $UnioncommentModel = new UnioncommentModel;
        $index['list'] = $UnioncommentModel->field("id,KP_KhName,KP_KhPhone,KP_Content,KP_Time,KP_Reply,KP_ServiceName,KP_ServiceTel
                                                    ,KP_FlagHf,KP_Check")
                                             ->where(["KP_Check"=>1])
                                             ->limit($pages,$pagesize)
                                             ->order("id desc")->select();                   
         $index['total'] = $UnioncommentModel->field("id")
                                             ->where(["KP_Check"=>1])
                                             ->order("id desc")->count("id");
        return json(collection($index)->toArray());                                     
    }


    //楼盘点评 KP_LpID KP_Score KP_Phone KP_Content KP_Time KP_Agree KP_Disapprove KP_Check KP_KhName
    public function comment(){
        $UnioncommentModel = new UnioncommentModel;
        $params = $this->request->post();
        if ($params) {
            try {
                $param['KP_KhName'] = $params['T_KhName'];
                $param['KP_KhPhone'] = $params['T_KhPhone'];
                $param['KP_Content'] = $params['T_Content'];
                $param['KP_Time'] = date("Y-m-d H:i:s");
                $result =  $UnioncommentModel->allowField(true)->save($param);
                if ($result !== false) {
                    return json(array('info'=>'Yes'));
                } else {
                    return json(array('info'=>$UnioncommentModel->getError()));
                }
            } catch (\think\exception\PDOException $e) {
                return json(array('info'=>$e->getMessage()));
            } catch (\think\Exception $e) {
                return json(array('info'=>$e->getMessage()));
            }
        }
    }

}
