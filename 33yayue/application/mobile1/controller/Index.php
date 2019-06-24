<?php

namespace app\mobile1\controller;

use app\common\controller\Frontend;
use app\common\library\Token;
use app\admin\model\Banner as BannerModel;
use app\admin\model\Lptg as LptgModel;
use app\admin\model\Lp as LpModel;
use app\admin\model\Area as AreaModel;
use app\admin\model\Ywtype as YwtypeModel;
use app\admin\model\Activityad as ActivityadModel;
use app\admin\model\News as NewsModel;
use app\admin\model\Lpreview as LpreviewModel;
use app\admin\model\Lpprice as LppriceModel;
use app\admin\model\Zhcity as ZhcityModel;
class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
        //区域
    }

    public function index()
    {
 
        //幻灯片
        $BannerModel = new BannerModel();
        $AreaModel = new AreaModel;
        $ZhcityModel = new ZhcityModel;
        $wheres = '1=1';
        if ($this->City) {
            $CLEVEL = $ZhcityModel->where('id',$this->City)->value('CLEVEL');
            if ($CLEVEL==1) {
                $wheres = 'KP_Provice='.$this->City.' and KP_Citys=0 and KP_District=0 and KP_County=0';
            }elseif($CLEVEL==2){
                $wheres = 'KP_Citys='.$this->City.' and KP_District=0 and KP_County=0';
            }elseif($CLEVEL==3){
                $wheres = 'KP_District='.$this->City.' and KP_County=0';
            }else{
                $wheres = 'KP_County='.$this->City;
            }
        }
        
        $hdplist = $BannerModel->field('id,KP_Title,KP_PicUrl,KP_Link')
                                    ->where(['KP_Type'=>0,'KP_Switch'=>1])
                                    ->where($wheres)
                                    ->order('weigh Desc')
                                    ->limit(5)
                                    ->select();
        if (count($hdplist)==0) {
            if (!$this->City) {
              $where = 'KP_Provice='.$this->City;
            }else{
              $hdpareaid = $AreaModel->where('areaid',$this->City)->value('KP_Provice'); 
              $where = 'KP_Provice='.$hdpareaid;
            }
            
            $hdplist = $BannerModel->field('KP_Title,KP_PicUrl,KP_Link')
                                    ->where(['KP_Type'=>0,'KP_Switch'=>1])
                                    ->where($where)
                                    ->order('weigh Desc')
                                    ->limit(5)
                                    ->select();
            $index['hdplist'] = $hdplist;
        }else{
            $index['hdplist'] = $hdplist;
        }
        //筛选部分
        $index['sxqy'] = $AreaModel->field('id,name,areaid')
                                   ->where($this->WhereCity)
                                   ->order('weigh Desc')
                                   ->select();
        $YwtypeModel = new YwtypeModel;      
        //$index['sxlx'] = $YwtypeModel->field('id,KP_Name')->where('KP_Type',0)->limit(10)->select();
        $index['sxts'] = $YwtypeModel->field('id,KP_Name')->where('KP_Type',1)->limit(10)->select();
        $index['sxjg'] = $YwtypeModel->field('id,KP_Name,KP_Content')->where('KP_Type',2)->limit(10)->select(); 

        //楼市快讯
        $NewsModel = new NewsModel;
        $index['lskx'] = $NewsModel->field('id,KP_Title,KP_PicUrl,KP_AddTime,KP_Cs,KP_Description')
                                   ->where('KP_Lmid','<>',10)
                                   ->where($this->WhereCity)
                                   ->order('id desc')->limit(5)
                                   ->select();
        foreach ($index['lskx'] as $key => $value) {
            $value['KP_AddTime'] = date('Y-m-d',strtotime($value['KP_AddTime']));
        }
        /*$index['lskx'] = $NewsModel->alias('a')
                                   ->join('newslm b','a.KP_Lmid=b.id')
                                   ->field('a.id,a.KP_Title,a.KP_PicUrl,b.KP_Name')
                                   ->where('a.KP_Lmid','<>',10)
                                   ->where($this->WhereCityA)
                                   ->order('a.id desc')->limit(5)
                                   ->select();*/

        //头天快报
        $index['ttkb'] = $NewsModel->field('id,KP_Title')
                                   ->where('KP_Ontop',1)
                                   ->where($this->WhereCity)
                                   ->order('id Desc')->limit(5)
                                   ->select();
        //新房推荐
        $LpModel = new LpModel;
        $index['xftj'] = $LpModel->alias('a')
                        ->field('a.id,a.KP_City,a.KP_Provice,a.KP_LpName,a.KP_Wjt,a.KP_Xszt,a.KP_Tel,a.KP_Lpdz,a.KP_TaoJia,a.KP_TsType')
                        ->where($this->WhereCityA)
                        ->limit(5)
                        ->order('KP_EditTime desc,KP_Tj desc')
                        ->select();

        $arr = array();
        foreach ($index['xftj'] as $key => $value) {
            $arr[] = $value['id'];
        }      
        $LppriceModel = new LppriceModel; 
        $prices = $LppriceModel
                    ->field('KP_LpID,KP_Qiprice,KP_Juprice,KP_ValidityStart,KP_ValidityEnd')
                    ->where('KP_LpID','in',$arr)
                    ->order('id asc')
                    ->select();
        $pricearr = array();            
        foreach ($prices as $key => $value) {
            $pricearr[$value['KP_LpID']] = $value;
        }
        foreach ($index['xftj'] as $key => $value) {
            if (isset($pricearr[$value['id']])) {
                $value['KP_Qiprice'] = floatval($pricearr[$value['id']]['KP_Qiprice']);
                $value['KP_Juprice'] = floatval($pricearr[$value['id']]['KP_Juprice']);
                $value['KP_ValidityStart'] = date("Y年m月d日",strtotime($pricearr[$value['id']]['KP_ValidityStart']));
                $value['KP_ValidityEnd'] = date("Y年m月d日",strtotime($pricearr[$value['id']]['KP_ValidityEnd']));
            }else{
                $value['KP_Qiprice'] = 0;
                $value['KP_Juprice'] = 0;
                $value['KP_ValidityStart'] = 0;
                $value['KP_ValidityEnd'] = 0;
            }
            $value['CNAME'] = db('zhcity')->where('id',$value['KP_Provice'])->cache()->value('CNAMES').'-'.db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
            $value['KP_Price'] = $value['KP_Juprice']?$value['KP_Juprice']:($value['KP_TaoJia']?$value['KP_TaoJia']:'待定');
            $value['KP_Price_unit'] = $value['KP_Juprice']?'元/㎡':($value['KP_TaoJia']?'万元/套':'');
            $value['KP_Price_title'] = $value['KP_Juprice']?'均价：':($value['KP_TaoJia']?'套价：':'');
        } 
        $this->view->assign('index',$index);
       
      
         $this->view->assign('areatext',$AreaModel->where('areaid',$this->City)->value('name'));
        return $this->view->fetch();
    }
   
   public function selectqy(){
        $AreaModel = new AreaModel;
        $quyu = $AreaModel->field('id,name,areaid,domain')
                                   ->where("areapid",0)
                                   ->where("status",1)
                                   ->order('weigh Desc')
                                   ->cache("area_data")
                                   ->select();
        foreach ($quyu as $key => $value) {
           $quyu[$key]['quyu'] = $AreaModel->field('id,name,areaid,domain')
                                                   ->where("KP_Provice|KP_Citys|KP_District|KP_County",$value['areaid'])
                                                   ->where("areapid",'<>',0)
                                                   ->where("status",1)
                                                   ->order('weigh Desc')
                                                   ->cache('area_data'.$key)
                                                   ->select();
        }
        $this->view->assign('quyu',$quyu);
        $this->view->engine->layout('layout/blankabc' . $this->layout);
        return $this->view->fetch("index/selectqy");

    }

    public function userdz(){
        return $this->view->fetch("index/userdz");
    }

    public function news()
    {
        echo "echo";
        // return jsonp(['newslist' => $newslist, 'new' => count($newslist), 'url' => 'https://www.fastadmin.net?ref=news']);
    }

    public function Search(){

         return $this->view->fetch("index/msearch");
    }

    public function getLpData(){
         $params = $this->request->post();
         $LpModel = new LpModel;
         $str1 = $this->zhongtoshu($params['keyword']);
         $str2 = $this->shutozhong($params['keyword']);
         $soList = $LpModel->field("id,KP_LpName,KP_City")->where('KP_LpName','like','%'.$str1.'%')->whereOr('KP_LpName','like','%'.$str2.'%')->limit(10)->select();
         foreach ($soList as $key => $value) {
            $value['KP_City'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
         }
         return json(collection($soList)->toArray());  
    }

    function shutozhong($time){
        if(!empty($time)){
            $labo = array( 0=>"零" , 1=> "一", 2=>"二" , 3=>"三" , 4 =>"四", 5=>"五" , 6=>"六" ,7 => "七", 8=>"八" , 9=> "九" );
            //拆分含有中文的字符串
            $arrTime = preg_split('/(?<!^)(?!$)/u', $time);
            foreach ($arrTime as $key => $value){
                if (is_numeric($value)) {
                    $arrTime[$key] = $labo[$value];
                }else{
                    $arrTime[$key] = $value;
                }
            }
            return implode("", $arrTime);
        }else{
            return $time;
        }
    }
    function zhongtoshu($time){
        if(!empty($time)){
            $han = array("零" => 0,"一" => 1,"二" => 2,"三" => 3,"四" => 4,"五" => 5,"六" => 6,"七" => 7,"八" => 8,"九" => 9);  
            $g= [
                "零","一","二","三","四","五","六","七","八","九"
            ];
            //拆分含有中文的字符串
            $arrTime = preg_split('/(?<!^)(?!$)/u', $time);
            foreach ($arrTime as $key => $value){
                if(in_array($value, $g)){
                    $arrTime[$key] = $han[$value];
                }else{
                    $arrTime[$key] = $value;                    
                }
            }
            return implode("", $arrTime);
        }else{
            return $time;
        }
    }

    public function bohao($ids="")
    {
 
         
         
        
        if ($ids) {
            $LpModel = new LpModel;
            $index['row'] = $LpModel->field('id,KP_Wjt,KP_LpName,KP_YouHui,KP_Tel')->where('id',$ids)->find();
            $index['id'] = $ids;
        }else{
            $index['row'] = array();
            $index['id'] = 0;
        }
        
        $this->view->assign('index',$index);
        $this->view->engine->layout('layout/blankabc'); 
        return $this->view->fetch();
    }
}
