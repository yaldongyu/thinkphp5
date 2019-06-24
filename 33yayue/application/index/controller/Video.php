<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\common\library\Token;
use app\admin\model\Video as VideoModel;
use app\admin\model\Lp as LpModel;
use app\admin\model\Lpprice as LppriceModel;
use think\Db;
class Video extends Frontend
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
        $VideoModel = new VideoModel;
        //视频看房 
        $index['list'] = $VideoModel
                            ->field('id,KP_LpID,KP_Title,KP_VideoUrl,KP_PicUrl')
                            ->where('KP_Type',0)
                            ->limit(9)
                            ->select();
        $lpid = array();
        foreach ($index['list'] as $key => $value) {
            $lpid[] = $value['KP_LpID'];
        }
        $LpModel = new LpModel;
          $lparr = $LpModel
                      ->field('id,KP_Lpdz,KP_TaoJia,KP_AllViewUrl')
                      ->where("id",'in',$lpid)
                      ->select();
        
          $LppriceModel = new LppriceModel; 
          $prices = $LppriceModel
                      ->field('KP_LpID,KP_Juprice')
                      ->where('KP_LpID','in',$lpid)
                      ->group('KP_LpID')
                      ->select();
          $pricearr = array();            
          foreach ($prices as $key => $value) {
              $pricearr[$value['KP_LpID']] = $value;
          }
          $lpinfoarr = array();
          foreach ($lparr as $key3 => $value) {
              if (isset($pricearr[$value['id']])) {
                  $value['KP_Juprice'] = floatval($pricearr[$value['id']]['KP_Juprice']);
              }else{
                  $value['KP_Juprice'] = 0;
              }
              $lpinfoarr[$value['id']] = $value;
          } 
        

        foreach ($index['list'] as $key => $value) {
            $value['KP_Lpdz'] = isset($lpinfoarr[$value['KP_LpID']]['KP_Lpdz'])?$lpinfoarr[$value['KP_LpID']]['KP_Lpdz']:'';
            $value['KP_TaoJia'] = isset($lpinfoarr[$value['KP_LpID']]['KP_TaoJia'])?$lpinfoarr[$value['KP_LpID']]['KP_TaoJia']:'';
            $value['KP_Juprice'] = isset($lpinfoarr[$value['KP_LpID']]['KP_Juprice'])?$lpinfoarr[$value['KP_LpID']]['KP_Juprice']:'';
            $value['KP_OverallUrl'] = isset($lpinfoarr[$value['KP_LpID']]['KP_AllViewUrl'])?$lpinfoarr[$value['KP_LpID']]['KP_AllViewUrl']:'';
        }

        $index['total'] = $VideoModel->field('id')
               ->where('KP_Type',0)
               //->where($this->WhereCity)
               ->count("id");
        $this->view->assign("index",$index);
        //print_r(collection($index['list'])->toArray()); exit;                        
        return $this->view->fetch("index/video");
    }

    public function detail($ids=''){
          
            $VideoModel = new VideoModel;
            //视频内容
            $index = VideoModel::get($ids);
            $types = VideoModel::getTypeList();
            $index['typename'] = $types[$index['KP_Type']];
            /*//楼盘信息
            $LpModel = new LpModel;
            $LppriceModel = new LppriceModel;
            $index['Lpinfo'] = $LpModel->field('id,KP_LpName,KP_YouHui,KP_Citys,KP_Wjt,KP_TaoJia')->where('id',$index['content']['KP_LpID'])->find();
            $priceobj = $LppriceModel->field('KP_Qiprice,KP_Juprice')->where('KP_LpID',$index['content']['KP_LpID'])->find();
            $index['Lpinfo']['KP_Qiprice'] = floatval($priceobj['KP_Qiprice']);
            $index['Lpinfo']['KP_Juprice'] = floatval($priceobj['KP_Juprice']);*/
            //更新播放量
            $VideoModel->where('id', $ids)->setInc('KP_Cs');
            //print_r(collection($index['Lpinfo'])->toArray()); exit;      
            $this->view->assign("row",$index);     
            return $this->view->fetch("index/video_details");
    }

    public function videolist($ids=''){
        $this->view->assign("id",$ids);   
        return $this->view->fetch("index/videolist");
    }
   //视频看房ajax
   public function videopage(){

        $params = $this->request->post(); 
        $page = isset($params['page'])?($params['page']?$params['page']:1):1; 
        $pagesize = isset($params['pagesize'])?$params['pagesize']:10;
        $pages = ($page-1)*$pagesize;
        $type = isset($params['type'])?$params['type']:-1;

        $VideoModel = new VideoModel;
        //视频看房
        $index['list'] = $VideoModel
                            ->field('id,KP_LpID,KP_Title,KP_VideoUrl,KP_OverallUrl,KP_PicUrl')
                            ->where('KP_Type',$type)
                            ->limit($pages,$pagesize)
                            ->select();
        $lpid = array();
        foreach ($index['list'] as $key => $value) {
            $lpid[] = $value['KP_LpID'];
        }
        $LpModel = new LpModel;
          $lparr = $LpModel
                      ->field('id,KP_Lpdz,KP_TaoJia')
                      ->where("id",'in',$lpid)
                      ->select();
        
          $LppriceModel = new LppriceModel; 
          $prices = $LppriceModel
                      ->field('KP_LpID,KP_Juprice')
                      ->where('KP_LpID','in',$lpid)
                      ->group('KP_LpID')
                      ->select();
          $pricearr = array();            
          foreach ($prices as $key => $value) {
              $pricearr[$value['KP_LpID']] = $value;
          }
          $lpinfoarr = array();
          foreach ($lparr as $key3 => $value) {
              if (isset($pricearr[$value['id']])) {
                  $value['KP_Juprice'] = floatval($pricearr[$value['id']]['KP_Juprice']);
              }else{
                  $value['KP_Juprice'] = 0;
              }
              $lpinfoarr[$value['id']] = $value;
          } 
        

        foreach ($index['list'] as $key => $value) {
            $value['KP_Lpdz'] = isset($lpinfoarr[$value['KP_LpID']]['KP_Lpdz'])?$lpinfoarr[$value['KP_LpID']]['KP_Lpdz']:'';
            $value['KP_TaoJia'] = isset($lpinfoarr[$value['KP_LpID']]['KP_TaoJia'])?$lpinfoarr[$value['KP_LpID']]['KP_TaoJia']:'';
            $value['KP_Juprice'] = isset($lpinfoarr[$value['KP_LpID']]['KP_Juprice'])?$lpinfoarr[$value['KP_LpID']]['KP_Juprice']:'';
        }
        $index['total'] = $VideoModel->field('id')
               ->where('KP_Type',$type)
               //->where($this->WhereCity)
               ->count("id");
        $data = collection($index)->toArray();
        return json($data);
   }
   //景点看房
    public function  jing(){
        $VideoModel = new VideoModel;
        //视频看房
        $index['list'] = $VideoModel
                            ->field('id,KP_Title,KP_PicUrl')
                            ->where('KP_Type',1)
                            ->limit(9)
                            ->select();
         $this->view->assign("index",$index);
         return $this->view->fetch("index/videojing");
    }
//景点看房ajax
    public function  jingpage(){
        $params = $this->request->post(); 
        $page = isset($params['page'])?($params['page']?$params['page']:1):1; 
        $pagesize = isset($params['pagesize'])?$params['pagesize']:10;
        $pages = ($page-1)*$pagesize;
        $type = isset($params['type'])?$params['type']:-1;
        $VideoModel = new VideoModel;
        //视频看房
        $index['list'] = $VideoModel
                            ->field('id,KP_Title,KP_PicUrl')
                            ->where('KP_Type',$type)
                            ->limit($pages,$pagesize)
                            ->select();
        $index['total'] = $VideoModel->field('id')
               ->where('KP_Type',$type)
               //->where($this->WhereCity)
               ->count("id");
        $data = collection($index)->toArray();
        return json($data);
    }

}
