<?php

namespace app\mobile\controller;

use app\common\controller\Frontend;
use app\common\library\Token;
use think\Db;
class Zhuanti extends Frontend
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
        return $this->view->fetch("index/zhuanti");
    }

    public function detail($ids=''){
            //视频内容
            /*$index['content'] = VideoModel::get($ids);
            //楼盘信息
            $LpModel = new LpModel;
            $LppriceModel = new LppriceModel;
            $index['Lpinfo'] = $LpModel->field('id,KP_LpName,KP_YouHui,KP_Citys,KP_Wjt,KP_TaoJia')->where('id',$index['content']['KP_LpID'])->find();
            $priceobj = $LppriceModel->field('KP_Qiprice,KP_Juprice')->where('KP_LpID',$index['content']['KP_LpID'])->find();
            $index['Lpinfo']['KP_Qiprice'] = floatval($priceobj['KP_Qiprice']);
            $index['Lpinfo']['KP_Juprice'] = floatval($priceobj['KP_Juprice']);
            //更新播放量
            $NewsModel->where('id', $ids)->setInc('KP_Cs');
            print_r(collection($index['Lpinfo'])->toArray()); exit;       */    
            return $this->view->fetch("index/zhuanti");
    }
   


}
