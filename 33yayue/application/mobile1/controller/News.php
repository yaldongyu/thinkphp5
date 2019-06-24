<?php

namespace app\mobile1\controller;

use app\common\controller\Frontend;
use app\common\library\Token;
use app\admin\model\News as NewsModel;
use app\admin\model\Newslm as NewslmModel;
use app\admin\model\Housecjdata as HousecjdataModel;
use app\admin\model\Citycjdata as CitycjdataModel;
use app\admin\model\Lp as LpModel;
use app\admin\model\Lptg as LptgModel;
use app\admin\model\Lpprice as LppriceModel;
use app\admin\model\Lpalbum as LpalbumModel;
use app\admin\model\Lpreview as LpreviewModel;
use think\Db;
class News extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index($typeid=2)
    {

        $NewsModel = new NewsModel;
        $index['hdp'] = $NewsModel->field('id,KP_Title,KP_PicUrl')
                                   ->where('KP_Lmid','<>',10)
                                   ->where('KP_Waphd',1)
                                   ->where($this->WhereCity)
                                   ->order('id desc')->limit(3)
                                   ->select();
        $index['list'] = $NewsModel
                                   ->field('id,KP_Title,KP_Description,KP_AddTime,KP_PicUrl')
                                   ->where('KP_Lmid',$typeid)
                                   ->where($this->WhereCity)
                                   ->order('id desc,KP_Cs Desc')->limit(20)
                                   ->select();  
        $index['total'] = $NewsModel
                                   ->field('id')
                                   ->where('KP_Lmid',$typeid)
                                   ->where($this->WhereCity)
                                   ->count('id');                           
        //print_r(collection($index['list'])->toArray());exit;     
        $this->view->assign("index",$index);                   
        return $this->view->fetch("index/news");
    }

    public function indexapi()
    {
        $params = $this->request->post();
        $page = isset($params['page'])?($params['page']?$params['page']:1):1; 
        $pagesize = isset($params['pagesize'])?$params['pagesize']:20;
        $pages = ($page-1)*$pagesize;
        $typeid = isset($params['typeid'])?$params['typeid']:'2';
        $typearr = explode(',',$typeid);
        $NewsModel = new NewsModel;
        $index['list'] = $NewsModel->field('id,KP_Title,KP_Lmid,KP_Description,KP_AddTime,KP_PicUrl')
                                   ->where('KP_Lmid','in',$typearr)
                                   ->where($this->WhereCity)
                                   ->order('id desc,KP_Cs Desc')->limit($pages,$pagesize)
                                   ->select();  
        $index['total'] = $NewsModel->field('id')
                                   ->where('KP_Lmid','in',$typearr)
                                   ->where($this->WhereCity)
                                   ->count('id');      
        $NewslmModel = new NewslmModel;
        foreach ($index['list'] as $key => $value) {
           $value['KP_AddTime'] = date('Y-m-d',strtotime($value['KP_AddTime']));
           $value['KP_Typename'] = $NewslmModel->where('id',$value['KP_Lmid'])->value('KP_Name');
         }                     
        $data = collection($index)->toArray();
        return json($data);
    }

   //文章详细
    public function detail($ids=''){
        $NewsModel = new NewsModel;
        //文章详细内容
        $index['content'] = NewsModel::get($ids);
        $index['content']['KP_AddTime'] = date('Y-m-d',strtotime($index['content']['KP_AddTime']));
        $LpModel = new LpModel;
        //如果有楼盘id就显示楼盘信息
        if ($index['content']['KP_LpID']) {
            //楼盘信息
            $LppriceModel = new LppriceModel;
              $index['LpInfo'] = $LpModel->field('id,KP_LpName,KP_Zxqk,KP_Xszt,KP_Tel,KP_Wylx,KP_Lpdz,KP_Logo,KP_TaoJia')->where('id',$index['content']['KP_LpID'])->find();
            // $index['LpInfo'] = $LpModel->field('id,KP_Zxqk,KP_Wylx,KP_Lpdz,KP_Logo')->where('id',$index['content']['KP_LpID'])->find();
            $index['LpInfo']['KP_Juprice'] = floatval($LppriceModel->where('KP_LpID',$index['content']['KP_LpID'])->order('id desc')->value('KP_Juprice')); 
            $zx = LpModel::getTypeList();
            $index['LpInfo']['KP_Zxqk'] = $zx[$index['LpInfo']['KP_Zxqk']];
        }
        $index['prev'] = $NewsModel->field('id,KP_Title')->where('id','<',$ids)->where('KP_Lmid',$index['content']['KP_Lmid'])->order('id desc')->find();
        $index['next'] = $NewsModel->field('id,KP_Title')->where('id','>',$ids)->where('KP_Lmid',$index['content']['KP_Lmid'])->order('id asc')->find();

        //为你推荐
        $index['tjlp'] = $LpModel->alias("a")
                         ->field('a.id,a.KP_Wjt,a.KP_LpName,KP_Xszt,KP_TsType,a.KP_TaoJia,a.KP_City,a.KP_Provice')
                         ->where($this->WhereCityA)
                         ->where('a.id','<>',$ids)
                         ->where("a.KP_Tj",1)
                         ->where('KP_Xszt','in',[0,1])
                         ->order('a.KP_Cs desc ,a.KP_EditTime desc')
                         ->limit(4)
                         ->select();

        $arr = array();
        foreach ($index['tjlp'] as $key => $value) {
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
        foreach ($index['tjlp'] as $key => $value) {
            if (isset($pricearr[$value['id']])) {
                $value['KP_Qiprice'] = floatval($pricearr[$value['id']]['KP_Qiprice']);
                $value['KP_Juprice'] = floatval($pricearr[$value['id']]['KP_Juprice']);
            }else{
                $value['KP_Qiprice'] = 0;
                $value['KP_Juprice'] = 0;
            }
            $value['CNAME'] = db('zhcity')->where('id',$value['KP_Provice'])->cache()->value('CNAMES').'-'.db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
            $value['KP_Wjt'] = str_replace('Max', 'Min', $value['KP_Wjt']);
            $value['KP_Price'] = $value['KP_Juprice']?$value['KP_Juprice']:($value['KP_TaoJia']?$value['KP_TaoJia']:'待定');
            $value['KP_Price_unit'] = $value['KP_Juprice']?'元/㎡':($value['KP_TaoJia']?'万元/套':'');
            $value['KP_Price_title'] = $value['KP_Juprice']?'均价：':($value['KP_TaoJia']?'套价：':'');
        } 
        //更新文章阅读量
        $NewsModel->where('id', $ids)->setInc('KP_Cs');     
        $this->view->assign("index",$index);       
        return $this->view->fetch("index/news_detail");
    }

     
    public function newstype($typeid = ''){
        $NewsModel = new NewsModel;
        if ($typeid==11||$typeid==14) {
          $city = '1=1';
        }else{
          $city = $this->WhereCity;
        }
        $index = $NewsModel->field('id,KP_Title,KP_Description,KP_AddTime,KP_PicUrl')
               ->where('KP_Lmid',$typeid)
               ->where($city)
               ->order('KP_Cs Desc,id Desc')->limit(10)
               ->select();
        $this->view->assign("index",$index);       
        return $this->view->fetch("index/news");
    }

    //ajax获取数据，分类获取
    public function classnews(){
        $params = $this->request->post();
         
        $page = isset($params['page'])?($params['page']?$params['page']:1):1; 
        $pagesize = isset($params['pagesize'])?$params['pagesize']:10;
        $pages = ($page-1)*$pagesize;
        $type = isset($params['type'])?$params['type']:-1;
        if ($type==11||$type==14) {
          $city = '1=1';
        }else{
          $city = $this->WhereCity;
        }
        $NewsModel = new NewsModel;
        $index = $NewsModel->field('id,KP_Title,KP_Description,KP_AddTime,KP_PicUrl')
               ->where('KP_Lmid',$type)
               ->where($city)
               ->order('KP_Cs Desc,id Desc')->limit($pages,$pagesize)
               ->select();
        $data = collection($index)->toArray();
        return json($data);
    }

}
