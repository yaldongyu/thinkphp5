<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\admin\model\Aboutus as AboutusModel;
use app\common\library\Token;
use think\Db;
class About extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = 'index';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {               
        $AboutusModel = new AboutusModel;
        
        $doma = explode('.', $_SERVER['SERVER_NAME']);
        if ($doma[1]=='lvfangw') {
            $info = $AboutusModel->where('id','in','5,6,7,8')->select();
        }else{
            $info = $AboutusModel->where('id','in','1,2,3,4')->select();
        }
        //$siteaboutuslanmu=config('site.aboutuslanmu');
        //$info['KP_Type_txt'] = $siteaboutuslanmu[$info['KP_Type']];
        $this->view->assign("info",$info);
        return $this->view->fetch("index/about");
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
