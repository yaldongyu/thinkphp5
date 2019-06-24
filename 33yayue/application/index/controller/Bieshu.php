<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\common\library\Token;
use app\admin\model\Area as AreaModel;
use app\admin\model\Lp as LpModel;
use app\admin\model\Lpprice as LppriceModel;
use app\admin\model\Brandlp as BrandlpModel;
use think\Db;
class Bieshu extends Frontend
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
        $AreaModel = new AreaModel;
        if ($this->City=='') {
           $index['tjlptop'] = $AreaModel->field('id,name,areaid,picurl,desc')
                                   ->where('areapid',0)
                                   ->order('weigh Desc')
                                   ->limit(2)
                                   ->select();
        }else{
            $index['tjlptop'] = $AreaModel->field('id,name,areaid,picurl,desc')
                                   ->where($this->WhereCity)
                                   ->where('areapid','<>',0)
                                   ->order('weigh Desc')
                                   ->limit(2)
                                   ->select();
        }
        

        $arr = array();
        foreach ($index['tjlptop'] as $key => $value) {
            $arr[$key] = $value['areaid'];
        }
        $LppriceModel = new LppriceModel;
        $LpModel = new LpModel;
/*
        $lparr = $LpModel->field('id,KP_LpName,KP_Wjt,KP_City,KP_TaoJia')->where('KP_Tj',1)->where('KP_Provice|KP_Citys|KP_District|KP_County','in',$arr)->limit(4)->select();
        $lpparr = $LppriceModel->field('KP_Qiprice,KP_Juprice')->where('KP_LpID','in',)->select();*/
         /*$lparr = $LpModel->alias('a')
                        ->join('lpprice b','a.id=b.KP_LpID','left')
                        ->field('a.id,a.KP_LpName,a.KP_Wjt,a.KP_City,a.KP_TaoJia,b.KP_Qiprice,b.KP_Juprice')
                        ->where(['KP_Tj'=>1])->where('KP_Provice|KP_Citys|KP_District|KP_County','in',$arr)->limit(4)->select();
        $lpids = array();
        foreach ($lparr as $key => $value) {
            $lpids[$key] = $value['id'];
        }
*/
       // print_r(collection($lparr)->toArray());exit;
        foreach ($index['tjlptop'] as $key => $value) {
            $index['tjlptop'][$key]['lp'] = $LpModel->alias('a')
                                                    ->join('lpprice b','a.id=b.KP_LpID')
                                                    ->field('a.id,a.KP_LpName,a.KP_Wjt,a.KP_City,a.KP_TaoJia,b.KP_Qiprice,b.KP_Juprice')
                                                    ->where(['KP_Tj'=>1,'KP_Provice|KP_Citys|KP_District|KP_County'=>$value['areaid']])->limit(4)->select();
            foreach ($index['tjlptop'][$key]['lp'] as $k => $v) {
                $v['album'] = db('lpalbum')->alias('a')
                                            ->join('lppic b','a.id=b.KP_HcID')
                                            ->field('b.KP_PicUrl,b.id')
                                            ->where('a.KP_LpID',$v['id'])
                                            ->limit(3)->select();
                $v['CNAME'] = db('zhcity')->where('id',$v['KP_City'])->cache()->value('CNAME');
                $v['KP_Desc'] = db("lpactivity")->where('KP_LpID',$v['id'])->value('KP_Info'); 
                $v['KP_Qiprice'] = floatval($v['KP_Qiprice']);
                $v['KP_Juprice'] = floatval($v['KP_Juprice']);
            }
        }

        $index['tjlpbottom'] = $AreaModel->field('id,name,areaid,picurl,desc')
                                   ->where('KP_Provice|KP_Citys|KP_District|KP_County',$this->City)
                                   ->order('weigh Desc')
                                   ->limit(7)
                                   ->select();
   /*     $LpModel = new LpModel;
        foreach ($index['tjlpbottom'] as $key => $value) {
            $index['tjlpbottom'][$key]['lp'] = $LpModel->alias('a')
                                                    ->join('lpprice b','a.id=b.KP_LpID')
                                                    ->field('a.id,a.KP_LpName,a.KP_Wjt,a.KP_City,a.KP_TaoJia,b.KP_Qiprice,b.KP_Juprice')
                                                    ->where(['KP_Tj'=>1,'KP_Provice|KP_Citys|KP_District|KP_County'=>$value['areaid']])->limit(6)->select();
            foreach ($index['tjlpbottom'][$key]['lp'] as $k => $v) {
                $v['CNAME'] = db('zhcity')->where('id',$v['KP_City'])->cache()->value('CNAME');
                $v['KP_Qiprice'] = floatval($v['KP_Qiprice']);
                $v['KP_Juprice'] = floatval($v['KP_Juprice']);
            }
        } */

        $index['navnum'] = $AreaModel->field('id')
                                   ->where($this->WhereCity)
                                   ->count('id');
        //print_r(collection($index['tjlptop'])->toArray());  exit; 
        $this->view->assign("index",$index);                       
        return $this->view->fetch("index/bieshu");
    }

    public function api(){
        $AreaModel = new AreaModel;
        $index = $AreaModel->field('id,name,areaid,picurl,desc')
                                   ->where('KP_Provice|KP_Citys|KP_District|KP_County',$this->City)
                                   ->order('weigh Desc')
                                   ->limit(7)
                                   ->select();
        $LpModel = new LpModel;
        foreach ($index as $key => $value) {
            $index[$key]['lp'] = $LpModel->alias('a')
                                                    ->join('lpprice b','a.id=b.KP_LpID')
                                                    ->field('a.id,a.KP_LpName,a.KP_Wjt,a.KP_City,a.KP_TaoJia,b.KP_Qiprice,b.KP_Juprice')
                                                    ->where(['KP_Tj'=>1,'KP_Provice|KP_Citys|KP_District|KP_County'=>$value['areaid']])->limit(6)->select();
            foreach ($index[$key]['lp'] as $k => $v) {
                $v['CNAME'] = db('zhcity')->where('id',$v['KP_City'])->cache()->value('CNAME');
                $v['KP_Qiprice'] = floatval($v['KP_Qiprice']);
                $v['KP_Juprice'] = floatval($v['KP_Juprice']);
            }
        } 
        
        $data = collection($index)->toArray();
        return json($data);
    }


    public function bslist(){
        $LpModel = new LpModel;
        $BrandlpModel = new BrandlpModel;
        $index['jituan'] = $BrandlpModel->field('id,KP_Name,KP_Logo,KP_Brand')->order('weigh desc')->select();
        foreach ($index['jituan'] as $key => $value) {
            $brandid = explode(',', $value['KP_Brand']);
            $value['KP_Brand'] = $LpModel->alias('a')
                        ->field('a.id,a.KP_City,a.KP_LpName,a.KP_Wjt,a.KP_YouHui,a.KP_TaoJia,a.KP_Zlhx')
                        ->where("a.id",'in',$brandid)
                        ->order('a.id Desc')
                        ->select();

            $arr = array();
            foreach ($value['KP_Brand'] as $key => $v) {
                $arr[] = $v['id'];
            }      
            $LppriceModel = new LppriceModel; 
            $prices = $LppriceModel
                        ->field('KP_LpID,KP_Qiprice,KP_Juprice')
                        ->where('KP_LpID','in',$arr)
                        ->order('id asc')
                        ->select();
            $pricearr = array();            
            foreach ($prices as $key => $v) {
                $pricearr[$v['KP_LpID']] = $v;
            }
            foreach ($value['KP_Brand'] as $key => $v) {
                if (isset($pricearr[$v['id']])) {
                    $v['KP_Qiprice'] = floatval($pricearr[$v['id']]['KP_Qiprice']);
                    $v['KP_Juprice'] = floatval($pricearr[$v['id']]['KP_Juprice']);
                }else{
                    $v['KP_Qiprice'] = 0;
                    $v['KP_Juprice'] = 0;
                }
                $v['CNAME'] = db('zhcity')->where('id',$v['KP_City'])->cache()->value('CNAMES');
            }

        }
        $this->view->assign("index",$index);                       
        return $this->view->fetch("index/bslist");
    }
   

}
