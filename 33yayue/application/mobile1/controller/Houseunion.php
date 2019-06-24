<?php

namespace app\mobile1\controller;

use app\common\controller\Frontend;
use app\common\library\Token;
use app\admin\model\Lp as LpModel;
use app\admin\model\Lpcomment as LpcommentModel;
use app\admin\model\Gflmkf as GflmkfModel;
use app\admin\model\Unioncomment as UnioncommentModel;
use app\admin\model\News as NewsModel;
use app\admin\model\Lpprice as LppriceModel;

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
        $LppriceModel = new LppriceModel; 
        //联盟楼盘
        $index['lmlp'] = $LpModel
                        ->field('id,KP_Wjt,KP_LpName,KP_Gflm,KP_City,KP_Tel,KP_Fjh,KP_Xszt,KP_Wylx,KP_TsType,KP_Lpdz,KP_YouHui,KP_TaoJia,KP_Cs,KP_Zycs')
                        ->where($this->WhereCity)
                        ->where('KP_Gflm',1)
                        ->limit(5)
                        ->order('KP_Top desc,KP_EditTime desc')
                        ->select();

        $arr = array();
        foreach ($index['lmlp'] as $key => $value) {
            $arr[] = $value['id'];
        }      
        $LppriceModel = new LppriceModel; 
        $prices = $LppriceModel
                    ->field('KP_LpID,KP_Qiprice,KP_Juprice')
                    ->where('KP_LpID','in',$arr)
                    ->order('id asc')
                    ->select();
        $pricearr = array();            
        foreach ($prices as $key => $value) {
            $pricearr[$value['KP_LpID']] = $value;
        }
        foreach ($index['lmlp'] as $key => $value) {
            if (isset($pricearr[$value['id']])) {
                $value['KP_Qiprice'] = floatval($pricearr[$value['id']]['KP_Qiprice']);
                $value['KP_Juprice'] = floatval($pricearr[$value['id']]['KP_Juprice']);
            }else{
                $value['KP_Qiprice'] = 0;
                $value['KP_Juprice'] = 0;
            }
            $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
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
                           ->order('id desc')->limit(5)
                           ->select();                                  
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
