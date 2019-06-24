<?php
 
namespace app\index\controller;

use app\common\controller\Frontend;
use app\common\library\Token;
use app\admin\model\Pcgeneralad as PcgeneraladModel;
use app\admin\model\Lptg as LptgModel;
use app\admin\model\Lp as LpModel;
use app\admin\model\Area as AreaModel;
use app\admin\model\Ywtype as YwtypeModel;
use app\admin\model\Activityad as ActivityadModel;
use app\admin\model\News as NewsModel;
use app\admin\model\Newslm as NewslmModel;
use app\admin\model\Lpreview as LpreviewModel;
use app\admin\model\Lpprice as LppriceModel;
use app\admin\model\Personaltailor as PersonaltailorModel;
use app\admin\model\Chengjiao as ChengjiaoModel;
use app\admin\model\Video as VideoModel;
use app\admin\model\Lpactivity as LpactivityModel;
class Index extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = 'index';

    public function _initialize()
    {
        parent::_initialize();
        //区域
    }

    
    public function index()
    {   
         
         
      
        //幻灯片
        $AreaModel = new AreaModel;
        $PcgeneraladModel = new PcgeneraladModel();
        $hdplist = $PcgeneraladModel->field('KP_Title,KP_PicUrl,KP_Link')
                                    ->where(['KP_Type'=>0,'KP_Switch'=>1])
                                    ->where($this->WhereCity)
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
            
            $hdplist = $PcgeneraladModel->field('KP_Title,KP_PicUrl,KP_Link')
                                    ->where(['KP_Type'=>0,'KP_Switch'=>1])
                                    ->where($where)
                                    ->order('weigh Desc')
                                    ->limit(5)
                                    ->select();
            $index['hdplist'] = $hdplist;
        }else{
            $index['hdplist'] = $hdplist;
        }
        //筛选部分(房价走势)
        if ($this->City) {
            $provid = $AreaModel->where('areaid',$this->City)->value('KP_Provice');
            $index['sxqy'] = $AreaModel->field('id,name,areaid,provjuprice,curjuprice')
                                   ->where('KP_Provice',$provid)
                                   ->order('weigh Desc')
                                   ->cache()
                                   ->where("status",1)
                                   ->select();
        }else{
            $index['sxqy'] = $AreaModel->field('id,name,areaid,provjuprice,curjuprice')
                                   ->order('weigh Desc')
                                   ->cache()
                                   ->where("status",1)
                                   ->where('ishot',1)
                                   ->select();
        }
        
        
        $YwtypeModel = new YwtypeModel;      
        //$index['sxlx'] = $YwtypeModel->field('id,KP_Name')->where('KP_Type',0)->order('id')->limit(10)->select();
        $index['sxts'] = $YwtypeModel->field('id,KP_Name')->where('KP_Type',1)->limit(10)->select();
        $index['sxjg'] = $YwtypeModel->field('id,KP_Name,KP_Content')->where('KP_Type',2)->order('id')->limit(10)->select(); 

        //本周甄选
        $ActivityadModel = new ActivityadModel;
        $AreaModel = new AreaModel;
        $byzx1 = $ActivityadModel->where('KP_Type',1)
                                         ->where($this->WhereCity)
                                         ->where('KP_Switch',1)
                                         ->order('weigh desc')
                                         ->limit(2)
                                         ->select();
        $arr = array();
        foreach ($byzx1 as $key => $value) {
            $arr[] = $value['id'];
        }                                 
        if (count($byzx1)<2&&$this->City) {
            $limit = 2-count($byzx1);
            $areaid = $AreaModel->where('areaid',$this->City)->value('KP_Provice'); 
            $byzx2 = $ActivityadModel->where('KP_Type',1)
                                         ->where('id','not in',$arr)
                                         ->where('KP_Provice',$areaid)
                                         ->order('weigh desc')
                                         ->limit($limit)
                                         ->select();
            $index['byzx'] = array_merge($byzx1,$byzx2);
        }else{
            $index['byzx'] = $byzx1;
        }

        //热销楼盘
        $LpModel = new LpModel;
         $index['rxlp'] = $LpModel->alias('a')
                              ->field('a.id,a.KP_City,a.KP_LpName,a.KP_Wjt,a.KP_Zycs,a.KP_TaoJia,a.KP_YouHui,a.KP_TsType')
                              ->where("a.KP_Yhz",1)
                              ->where($this->WhereCityA)
                              ->order('a.KP_Top desc , a.KP_EditTime desc')
                              ->limit(8)
                              ->select();

          $arr = array();
          foreach ($index['rxlp'] as $key => $value) {
              $arr[] = $value['id'];
          }      
          $LppriceModel = new LppriceModel; 
          $prices = $LppriceModel
                      ->field('KP_LpID,KP_Qiprice,KP_Juprice')
                      ->where('KP_LpID','in',$arr)
                      ->group('KP_LpID')
                      ->select();
          $pricearr = array();            
          foreach ($prices as $key => $value) {
              $pricearr[$value['KP_LpID']] = $value;
          }
          foreach ($index['rxlp'] as $key3 => $value) {
              if (isset($pricearr[$value['id']])) {
                  $value['KP_Qiprice'] = floatval($pricearr[$value['id']]['KP_Qiprice']);
                  $value['KP_Juprice'] = floatval($pricearr[$value['id']]['KP_Juprice']);
              }else{
                  $value['KP_Qiprice'] = 0;
                  $value['KP_Juprice'] = 0;
              }
              $value['KP_Wjt'] = str_replace('Max', 'Cen', $value['KP_Wjt']);
              $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
          }   
        
        //最近成交
        $ChengjiaoModel = new ChengjiaoModel;
        $index['zjcj'] = $ChengjiaoModel->field('id,KP_LpName,KP_LpID,KP_Price,KP_Hx,KP_Time')->order('id desc')->limit(20)->select();
        foreach ($index['zjcj'] as $key => $value) {
            $value['KP_Time'] = date('Y-m-d',strtotime($value['KP_Time']));
        }
        //团购活动(房价走势,楼市成交下面广告)
        $ActivityadModel = new ActivityadModel;
        $AreaModel = new AreaModel;
        $tghd1 = $ActivityadModel->where('KP_Type',0)
                                         ->where($this->WhereCity)
                                         ->where('KP_Switch',1)
                                         ->order('weigh desc')
                                         ->limit(2)
                                         ->select();
        $arr = array();
        foreach ($tghd1 as $key => $value) {
            $arr[] = $value['id'];
        }                                 
        if (count($tghd1)<2&&$this->City) {
            $limit = 2-count($tghd1);
            $areaid = $AreaModel->where('areaid',$this->City)->value('KP_Provice'); 
            $tghd2 = $ActivityadModel->where('KP_Type',0)
                                         ->where('id','not in',$arr)
                                         ->where('KP_Provice',$areaid)
                                         ->order('weigh desc')
                                         ->limit($limit)
                                         ->select();
            $index['tghd'] = array_merge($tghd1,$tghd2);
        }else{
            $index['tghd'] = $tghd1;
        }

        //房产头条 
        $NewsModel = new NewsModel;
        $index['fctt'] = $NewsModel->alias('a')
                                   ->field('a.id,a.KP_Title,a.KP_PicUrl,KP_AddTime')
                                   ->where(['a.KP_Waphd'=>0,'a.KP_Ontop'=>1])
                                   ->where('a.KP_Lmid','<>',10) 
                                   ->where($this->WhereCityA)
                                   ->order('a.KP_Ontop Desc,a.id desc')
                                   ->limit(3)
                                   ->select(); 
        //楼盘热点
        $index['lprd'] = $NewsModel->alias('a')
                                   ->field('a.id,a.KP_Title,a.KP_PicUrl,KP_AddTime')
                                   ->where('a.KP_Lmid',2) 
                                   ->where($this->WhereCityA)
                                   ->order('KP_Waphd desc, a.id desc')
                                   ->limit(7)
                                   ->select(); 
        //楼盘导购
        $index['lpdg'] = $NewsModel->alias('a')
                                   ->field('a.id,a.KP_Title,a.KP_PicUrl,KP_AddTime')
                                   ->where('a.KP_Lmid',5) 
                                   ->where($this->WhereCityA)
                                   ->order('a.id desc')
                                   ->limit(7)
                                   ->select();   

        //楼市动态
        $index['lsdt'] = $NewsModel->alias('a')
                                   ->field('a.id,a.KP_Title,a.KP_PicUrl,KP_AddTime')
                                   ->where('a.KP_Lmid',11) 
                                   ->where($this->WhereCityA)
                                   ->order('a.id desc')
                                   ->limit(5)
                                   ->select();    

        //底部视频
        $VideoModel = new VideoModel;
        /*$index['dbsp'] = $VideoModel->field('id,KP_Title,KP_PicUrl,KP_VideoUrl,KP_LpID,KP_OverallUrl')->limit(4)->select();
        $arr = array();
        foreach($index['dbsp'] as $key=>$value){
            $arr[] = $value['id'];
        }
        $videolp = $LpModel->field('id,KP_LpName,KP_City')->where('id','in',$arr)->select(); 
        $lparr = array();
        foreach ($videolp as $key => $value) {
            $lparr[$value['id']] = $value;
        }                          
        foreach ($index['dbsp'] as $key => $value) {
            if (isset($lparr[$value['KP_LpID']])) {
                $value['KP_LpName'] = $lparr[$value['KP_LpID']]['KP_LpName'];
                $value['KP_City'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
            }else{
                $value['KP_LpName'] = '';
                $value['KP_City'] = '';
            }
        }*/

        $index['dbsp'] = $VideoModel->alias('a')
                        ->join('lp b','a.KP_LpID=b.id')
                        ->field('a.id,a.KP_Title,a.KP_PicUrl,a.KP_VideoUrl,a.KP_LpID,b.KP_LpName,b.KP_City,b.KP_Zlhx,b.KP_YouHui')
                        ->order('a.id desc')
                        ->limit(4)->select();   
        $arr = array();
        foreach($index['dbsp'] as $key=>$value){
            $arr[] = $value['KP_LpID'];
        }     
        $LpactivityModel = new LpactivityModel;    
        $lpyh = $LpactivityModel->field('KP_LpID,KP_Info')->where("KP_LpID",'in',$arr)->select();
        foreach ($lpyh as $key => $value) {
            $lpyharr[$value['KP_LpID']] = $value['KP_Info'];
        }
        foreach ($index['dbsp'] as $key => $value) {
              $value['KP_City'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
              $value['KP_YouHui'] = $lpyharr[$value['KP_LpID']];
        }                   
        $this->assign('index',$index);
        return $this->view->fetch('index/index');
    }


    public function newsapi(){
        $params = $this->request->post();
        $typeid = $params['typeid'];
        //楼市动态
        $NewsModel = new NewsModel;
        $newlist = $NewsModel->alias('a')
                                   ->field('a.id,a.KP_Title,a.KP_PicUrl,KP_AddTime')
                                   ->where('a.KP_Lmid',$typeid) 
                                   ->where($this->WhereCityA)
                                   ->order('a.id desc')
                                   ->limit(5)
                                   ->select();  
        return json(collection($newlist)->toArray());                             
    }
    
    
    //优选惠购api
    public function yxhgapi(){
        $LptgModel = new LptgModel;
        $AreaModel = new AreaModel;
        $lptg = $LptgModel
                        ->field("id,KP_PicUrl")
                        ->where('KP_Htzt',0)
                        ->where($this->WhereCity)
                        ->order('weigh Desc')->limit(2)->select();

        $arr = array();
        foreach ($lptg as $key => $value) {
            $arr[] = $value['id'];
        }

        if (count($lptg)<2&&$this->City) {
            $limit = 2-count($lptg);
            $areaid = $AreaModel->where('areaid',$this->City)->value('KP_Provice'); 
            $lptg1 = $LptgModel
                        ->field("id,KP_PicUrl")
                        ->where('KP_Htzt',0)
                        ->where('KP_Provice',$areaid)
                        ->order('weigh Desc')->limit($limit)->select();            
            $index['lptg'] = array_merge($lptg,$lptg1);
        }else{
            $index['lptg'] = $lptg;
        }
        return json(collection($index)->toArray());
    }


    //发现好房(一线海景,养生宜居,豪宅别墅)api
    public function fxhfapi(){
        $params = $this->request->post();
        $position = $params['position'];
        $tjarr = array(1=>"海景",2=>"养生",3=>"投资");
        $LpModel = new LpModel;
        $index['tjlp'] = $LpModel->alias('a')
                                 ->field('a.id,a.KP_LpName,a.KP_Wjt,a.KP_YouHui,a.KP_TaoJia,a.KP_Zycs,a.KP_TsType,KP_City')
                                 ->where("KP_TsType",'like','%'.$tjarr[$position].'%')
                                 ->where('KP_Tj',1)
                                 ->where('KP_Xszt','<>',2)
                                 ->where($this->WhereCityA)
                                 ->order('a.KP_Top desc , a.KP_EditTime Desc')
                                 ->limit(8)
                                 ->select();
        $arr = array();
        foreach ($index['tjlp'] as $key1 => $value) {
            $arr[] = $value['id'];
        }      
        $LppriceModel = new LppriceModel; 
        $prices = $LppriceModel
                    ->field('KP_LpID,KP_Qiprice,KP_Juprice')
                    ->where('KP_LpID','in',$arr)
                    ->group('KP_LpID')
                    ->select();
        $pricearr = array();            
        foreach ($prices as $key2 => $value) {
            $pricearr[$value['KP_LpID']] = $value;
        }
        foreach ($index['tjlp'] as $key3 => $value) {
            if (isset($pricearr[$value['id']])) {
                $value['KP_Qiprice'] = floatval($pricearr[$value['id']]['KP_Qiprice']);
                $value['KP_Juprice'] = floatval($pricearr[$value['id']]['KP_Juprice']);
            }else{
                $value['KP_Qiprice'] = 0;
                $value['KP_Juprice'] = 0;
            }

            $value['KP_Wjt'] = str_replace('Max', 'Cen', $value['KP_Wjt']);
            $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
        }                                                   
                                          
        return json(collection($index)->toArray());
    }

    function findNum($str=''){
        $str=trim($str);
        if(empty($str)){return '';}
        $result='';
        for($i=0;$i<strlen($str);$i++){
            if(is_numeric($str[$i])){
                $result.=$str[$i];
            }
        }
        return $result;
    }
    


    public function userdz(){
        $PersonaltailorModel = new PersonaltailorModel;
        $dzlist = $PersonaltailorModel->field('KP_Yxcity,KP_Name,KP_Tel')->order('id desc')->limit(20)->select();
        foreach ($dzlist as $key => $value) {
            $value['KP_Tel'] = substr_replace($value['KP_Tel'],'****',3,4);
        }
        $this->view->assign('dzlist',$dzlist);
        return $this->view->fetch("index/userdz");
    }
    public function map()
    {
        $YwtypeModel = new YwtypeModel;   
        $AreaModel = new AreaModel;   
        $index['sxqy'] = $AreaModel->field('id,name,areaid,areapid')
                                   ->order('weigh Desc')
                                   ->cache()
                                   ->where("status",1)
                                   ->select();
        $index['sxhx'] = $YwtypeModel->field('id,KP_Name,KP_Content')->where('KP_Type',4)->select();
        $index['sxts'] = $YwtypeModel->field('id,KP_Name')->where('KP_Type',1)->select();
        $index['sxjg'] = $YwtypeModel->field('id,KP_Name,KP_Content')->where('KP_Type',2)->select();
        $this->view->assign('index',$index);
        return $this->view->fetch("index/map");
    }

    public function mapapi(){
        $LpModel = new LpModel;
        $params = $this->request->post();
        $city = $params['city'];
        $hx = $params['hx'];
        $price = $params['price'];
        $ts = $params['ts'];
        $sokey = $params['sokey'];
        if ($price) {
            $pricearr = explode('-', $price);
            $whereprice = 'b.KP_Qiprice > '.$pricearr[0].' and b.KP_Qiprice < '.$pricearr[1];
            
            $index['d'] = $LpModel->alias('a')
                            ->join('lpprice b','b.KP_LpID=a.id')
                            ->field('a.id,a.KP_City,a.KP_LpName as title,a.KP_Wjt as thumb,a.KP_Lpdz as h_address,a.KP_Tel as h_phone,substring_index(a.KP_Mapzb,",", 1) as h_mapx,substring_index(a.KP_Mapzb,",", -1) as h_mapy , "元/㎡" as h_pf,b.KP_Qiprice as s_price,b.KP_Juprice as h_price')
                            ->where("KP_TsType","like","%".$ts."%")
                            ->where("KP_Zlhx","like","%".$hx."%")
                            ->where("KP_LpName","like","%".$sokey."%")
                            ->where($whereprice)
                            ->where("a.KP_Provice|a.KP_Citys|a.KP_District|a.KP_County",$city)
                            ->order('a.KP_EditTime desc')
                            ->limit(50)
                            ->select();
                            
        }else{
            $index['d'] = $LpModel->alias('a')
                                    ->field('a.id,a.KP_City,a.KP_LpName as title,a.KP_Wjt as thumb,a.KP_Lpdz as h_address,a.KP_Tel as h_phone,substring_index(a.KP_Mapzb,",", 1) as h_mapx,substring_index(a.KP_Mapzb,",", -1) as h_mapy , "元/㎡" as h_pf')
                                    ->where("KP_TsType","like","%".$ts."%")
                                    ->where("KP_Zlhx","like","%".$hx."%")
                                    ->where("KP_LpName","like","%".$sokey."%")
                                    ->where("a.KP_Provice|a.KP_Citys|a.KP_District|a.KP_County",$city)
                                    ->order('a.KP_EditTime desc')
                                    ->limit(50)
                                    ->select();

            $arr = array();
            foreach ($index['d'] as $key1 => $value) {
                $arr[] = $value['id'];
            }      
            $LppriceModel = new LppriceModel; 
            $prices = $LppriceModel
                        ->field('KP_LpID,KP_Qiprice,KP_Juprice')
                        ->where('KP_LpID','in',$arr)
                        ->group('KP_LpID')
                        ->select();
            $pricearr = array();            
            foreach ($prices as $key2 => $value) {
                $pricearr[$value['KP_LpID']] = $value;
            }

            foreach ($index['d'] as $key3 => $value) {
                if (isset($pricearr[$value['id']])) {
                    $value['s_price'] = floatval($pricearr[$value['id']]['KP_Qiprice']);
                    $value['h_price'] = floatval($pricearr[$value['id']]['KP_Juprice']);
                }else{
                    $value['s_price'] = 0;
                    $value['h_price'] = 0;
                }
               
            }              
                                                                          
        }

        foreach ($index['d'] as $key3 => $value) {
                      $value['s_price'] = floatval($value['s_price']);
                      $value['h_price'] = floatval($value['h_price']);
                      $value['thumb'] = str_replace('Max', 'Min', $value['thumb']);
                  }  

        return json(collection($index)->toArray());
    }

    public function news()
    {
        $newslist = [];
        return "";
        // return jsonp(['newslist' => $newslist, 'new' => count($newslist), 'url' => 'https://www.fastadmin.net?ref=news']);
    }

    public function getLpData(){
         $params = $this->request->post();
         $LpModel = new LpModel;
         $str1 = $this->zhongtoshu($params['keyword']);
         $str2 = $this->shutozhong($params['keyword']);
         $soList = $LpModel->field("id,KP_LpName")->where('KP_LpName','like','%'.$str1.'%')
                                                  ->whereOr('KP_LpName','like','%'.$str2.'%')
                                                  ->whereOr('KP_TsType','like','%'.$params['keyword'].'%')
                                                  ->whereOr('KP_Lpdz','like','%'.$params['keyword'].'%')
                                                  ->whereOr('KP_Kfs','like','%'.$params['keyword'].'%')
                                                  ->whereOr("KP_Lppinyin",'like',$params['keyword'].'%')
                                                  ->whereOr("KP_Lppinyins",'like',$params['keyword'].'%')
                                                  ->limit(10)
                                                  ->select();
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

    //私人定制
    public function dingzhi(){
        $PersonaltailorModel = new PersonaltailorModel;
        $params = $this->request->post();
        if ($params) {
            try {
                $data['KP_Yxcity'] = $params['T_Yxcity'];
                $data['KP_Fwlx'] = $params['T_Fwlx'];
                $data['KP_Yxhx'] = $params['T_Yxhx'];
                $data['KP_Zyarea'] = $params['T_Zyarea'];
                $data['KP_Ysjw'] = $params['T_Ysjw'];
                $data['KP_Zxxq'] = $params['T_Zxxq'];
                $data['KP_Fjxq'] = $params['T_Fjxq'];
                $data['KP_Name'] = $params['T_Name'];
                $data['KP_Sex'] = $params['T_Sex'];
                $data['KP_Tel'] = $params['T_Tel'];
                $data['KP_AddTime'] = date('Y-m-d h:i:s');
                $result =  $PersonaltailorModel->allowField(true)->save($data);
                if ($result !== false) {
                    return json(['info'=>'Yes']);
                } else {
                    return json($PersonaltailorModel->getError());
                }
            } catch (\think\exception\PDOException $e) {
                return json($e->getMessage());
            } catch (\think\Exception $e) {
                return json($e->getMessage());
            }
        }
    }


}
