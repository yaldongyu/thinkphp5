<?php

namespace app\mobile\controller;

use app\common\controller\Frontend;
use app\common\library\Token;
use app\admin\model\Haozhai as HaozhaiModel;
use app\admin\model\Lp as LpModel;
use app\admin\model\Brandlp as BrandlpModel;
use app\admin\model\Area as AreaModel;
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
        $HaozhaiModel = new HaozhaiModel;
        $AreaModel = new AreaModel;
        $hzlist = $HaozhaiModel->alias('a')
                                   ->join('lp b','a.KP_LpID=b.id','left')
                                   ->field('a.id,a.KP_LpID,a.KP_Tel,a.KP_Ms,KP_Type,a.KP_PicUrl,a.KP_Name,a.KP_Fcyear,a.KP_Logo,b.KP_LpName')
                                   ->where($this->WhereCityA)
                                   ->order('a.weigh Desc')
                                   ->select();
        if (count($hzlist)==0) {
            if (!$this->City) {
              $where = 'a.KP_Provice='.$this->City;
            }else{
              $hdpareaid = $AreaModel->where('areaid',$this->City)->value('KP_Provice'); 
              $where = 'a.KP_Provice='.$hdpareaid;
            }
            
            $hzlist = $HaozhaiModel->alias('a')
                                   ->join('lp b','a.KP_LpID=b.id','left')
                                   ->field('a.id,a.KP_LpID,a.KP_Tel,a.KP_Ms,KP_Type,a.KP_PicUrl,a.KP_Name,a.KP_Fcyear,a.KP_Logo,b.KP_LpName')
                                   ->where($where)
                                   ->order('a.weigh Desc')
                                   ->select();
            $index['hzlist'] = $hzlist;
        }else{
            $index['hzlist'] = $hzlist;
        }
        $this->view->assign("index",$index);                       
        return $this->view->fetch("index/bieshu");
    }
   

}
