<?php

namespace app\index\controller;

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
use app\admin\model\Chengjiao as ChengjiaoModel;
use think\Db;
class News extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = 'default';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index($ids=0)
    {
        
        $NewsModel = new NewsModel;
        //热点头条
        $index['rdtt'] = $NewsModel->alias('a')
                                   ->join('newslm b','a.KP_Lmid=b.id')
                                   ->field('a.id,a.KP_Title,a.KP_PicUrl,a.KP_Description,b.KP_Name')
                                   ->where('a.KP_Waphd',1)
                                   ->where($this->WhereCityA)
                                   ->order('a.KP_Ontop Desc,a.id desc')->limit(4)
                                   ->select();
        
        //所有资讯                           
        if($ids==0){
          $index['allnews'] = $NewsModel->field('id,KP_Title,KP_Provice,KP_Description,KP_AddTime,KP_PicUrl') 
                                   ->where($this->WhereCity) 
                                   ->order('id desc')->limit(8)
                                   ->select();    
          $index['total'] = $NewsModel
                                   ->where($this->WhereCity)
                                   ->count('id');                         
        }else{
          $index['allnews'] = $NewsModel->field('id,KP_Title,KP_Provice,KP_Description,KP_AddTime,KP_PicUrl') 
                                   ->where($this->WhereCity)
                                   ->where('KP_Lmid',"=",$ids)
                                   ->order('id desc')->limit(8)
                                   ->select();
          $index['total'] = $NewsModel
                                   ->where($this->WhereCity)
                                   ->where('KP_Lmid',"=",$ids)
                                   ->count('id');                                                                             
        }
           
        foreach ($index['allnews'] as $key => $value) {
            $value['KP_City'] = db('zhcity')->where('id',$value['KP_Provice'])->cache()->value('CNAMES');
        }
        //每日热文推荐  
        $index['mrrwtj'] = $NewsModel
                                   ->field('id,KP_Title')
                                   ->where($this->WhereCity)
                                   ->order('KP_Tj desc,id desc')->limit(8)
                                   ->select();

        //商品成交记录
        $ChengjiaoModel = new ChengjiaoModel;
        $index['rmcjsh'] = $ChengjiaoModel->field('id,KP_LpName,KP_LpID,KP_Price,KP_Hx,KP_Time')->order('id desc')->limit(6)->select();          
        foreach ($index['rmcjsh'] as $key => $value) {
            $value['KP_Time'] = date('Y-m-d',strtotime($value["KP_Time"]));
        }              
        $index['type'] = $ids;
        //print_r(collection($index['tgyh'])->toArray());exit;     
        $this->view->assign("index",$index);                   
        return $this->view->fetch("index/news");
    }

   //文章详细
    public function detail($ids=''){
        $NewsModel = new NewsModel;
        //文章详细内容
        $index['content'] = NewsModel::get($ids);
        $NewslmModel = new NewslmModel;
        $index['content']['KP_LmName'] = $NewslmModel->where("id",$index['content']['KP_Lmid'])->value("KP_Name");
        

        //为你推荐
        $LpModel = new LpModel;
        if($index['content']['KP_LpID']>0){
          $index['content']['newslp'] = $LpModel->alias("a")
                         ->field('a.id,a.KP_Wjt,a.KP_LpName,a.KP_TaoJia,a.KP_City,a.KP_Tel') 
                         ->where('id','=', $index['content']['KP_LpID'] ) 
                         ->find();
        }        
        $index['tjlp'] = $LpModel->alias("a")
                         ->field('a.id,a.KP_Wjt,a.KP_LpName,a.KP_TaoJia,a.KP_City,a.KP_Provice')
                         ->where($this->WhereCityA)
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
            $value['KP_Price'] = $value['KP_Juprice']?$value['KP_Juprice'].'元/㎡':($value['KP_TaoJia']?$value['KP_TaoJia'].'万元/套':'待定');
            $value['CNAME'] = db('zhcity')->where('id',$value['KP_Provice'])->cache()->value('CNAMES').'-'.db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
            $value['KP_Wjt'] = str_replace('Max', 'Min', $value['KP_Wjt']);
        }    

        //相关资讯
        $index['xgzx'] = $NewsModel->field('id,KP_Title')->where('KP_Lmid',$index['content']['KP_Lmid'])->limit(5)->order('id desc')->select();
           
        //更新文章阅读量
        $NewsModel->where('id', $ids)->setInc('KP_Cs');  
        //print_r(collection($index['content'])); exit;        
       // print_r(collection($index['lpalbum'])->toArray()); exit;     
        $this->view->assign("index",$index);       
        return $this->view->fetch("index/news_detail");
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
        if ($type==0||$type==-1) {
           $where = '1=1';
        }else{
           $where = 'KP_Lmid='.$type; 
        }
        $NewsModel = new NewsModel;
        $index['list'] = $NewsModel->field('id,KP_Title,KP_Provice,KP_Description,KP_AddTime,KP_PicUrl')
               ->where($where)
               ->where($city)
               ->order('id Desc')->limit($pages,$pagesize)
               ->select();
        $index['total'] = $NewsModel->field('id')
               ->where($where)
               ->where($city)
               ->count('id');
        $index['id'] = $type;
        foreach ($index['list'] as $key => $value) {
              $value['KP_PicUrl'] = str_replace('Max', 'Cen', $value['KP_PicUrl']);
              $value['KP_City'] = db('zhcity')->where('id',$value['KP_Provice'])->cache()->value('CNAMES');
        }
        $data = collection($index)->toArray();
        return json($data);
    }

}
