<?php

namespace app\index\controller;
use \think\Request;
use app\common\controller\Frontend;
use app\common\library\Token;
use app\admin\model\Lp as LpModel;
use app\admin\model\Area as AreaModel;
use app\admin\model\Ywtype as YwtypeModel;
use app\admin\model\Lpalbum as LpalbumModel;
use app\admin\model\News as NewsModel;
use app\admin\model\Lphxpic as LphxpicModel;
use app\admin\model\Lppic as LppicModel;
use app\admin\model\Lpreview as LpreviewModel;
use app\admin\model\Lpprice as LppriceModel;
use app\admin\model\Lpcomment as LpcommentModel;
use app\admin\model\Kftbm as KftbmModel;
use app\admin\model\Lpcontent as LpcontentModel;
use app\admin\model\Lptgbm as LptgbmModel;
use app\admin\model\Lpactivity as LpactivityModel;
use app\admin\model\Zhcity;
use app\admin\model\Video as VideoModel;
use app\admin\model\Pcgeneralad as PcgeneraladModel;
use app\admin\model\Gflmkf as GflmkfModel;
use app\admin\model\Ysxkz as YsxkzModel;
use think\Db;
use think\Cookie;
class House extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize(){
        
        parent::_initialize();
        $this->view->assign('actionname',$this->acname);
    }

    //新房首页
    public function index(){
        $index['cookie'] = '';
        if (Cookie::get("Ts")) {
            $wheres = " KP_TsType like '%".urldecode(Cookie::get("Ts"))."%'";
            $index['cookie'] = 'Ts='.Cookie::get("Ts");
        }else{
            $wheres = "1=1";
        }
        
        if (Cookie::get("City")) {
           $wherec =  '(KP_Provice = '.Cookie::get("City").' or KP_Citys = '.Cookie::get("City").' or KP_District = '.Cookie::get("City").' or KP_County = '.Cookie::get("City").')';
           $index['cookie'] = 'City='.Cookie::get("City");
           //$index['provice'] = db()->where('')
        }else{
           $wherec = $this->WhereCity;
        }

        if (Cookie::get("Price")) {
            $pricearr = explode('-', urldecode(Cookie::get("Price")));
            $whereprice = 'b.KP_Qiprice > '.$pricearr[0].' and b.KP_Qiprice < '.$pricearr[1];
            $index['cookie'] = 'Price='.Cookie::get("Price");
        }else{
            $whereprice = "1=1";
        }
        
        if (Cookie::get("Sokey")) {
            $wheres = " KP_LpName like '%".urldecode(Cookie::get("Sokey"))."%' or "." KP_TsType like '%".urldecode(Cookie::get("Sokey"))."%' or "." KP_Lpdz like '%".urldecode(Cookie::get("Sokey"))."%' or "." KP_Kfs like '%".urldecode(Cookie::get("Sokey"))."%'";
            $wherec =  "1=1";
        }else{
            if (!Cookie::get("Ts")) {
                $wheres = "1=1";
            }
        }
        $index['Sokey'] = Cookie::get("Sokey");
        Cookie::set("Sokey","");
        //筛选部分
        $AreaModel = new AreaModel;
        if ($this->City) {
           $topid = $AreaModel->where('areaid',$this->City)->value('KP_Provice');
           $findcity = 'KP_Provice = '.$topid;
        }else{
           $findcity = $this->WhereCity;
        }
        
        $index['sxqy'] = $AreaModel->field('id,name,areaid')
                                   ->where($findcity)
                                   ->where("areapid",0)
                                   ->where('status',1)
                                   ->cache()
                                   ->order('weigh Desc')
                                   ->select();
        foreach ($index['sxqy'] as $key => $value) {
            if ($this->City) {
                $areawhere = '(KP_Provice = '.$this->City.' or KP_Citys = '.$this->City.' or KP_District = '.$this->City.' or KP_County = '.$this->City.')';
                $index['sxqy'][$key]['quyu'] = $AreaModel->field('id,name,areaid')
                                                   ->where($areawhere)
                                                   ->where("areapid",'<>',0)
                                                   ->where('status',1)
                                                   ->cache()
                                                   ->order('weigh Desc')
                                                   ->select();
            }else{
                $index['sxqy'][$key]['quyu'] = $AreaModel->field('id,name,areaid')
                                                   ->where('areapid',$value['areaid'])
                                                   ->where("areapid",'<>',0)
                                                   ->where('status',1)
                                                   ->cache()
                                                   ->order('weigh Desc')
                                                   ->select();
            }
           
           
        }

        $YwtypeModel = new YwtypeModel;      
        $index['sxhx'] = $YwtypeModel->field('id,KP_Name,KP_Content')->where('KP_Type',4)->select();
        $index['sxts'] = $YwtypeModel->field('id,KP_Name')->where('KP_Type',1)->limit(20)->select();
        $index['sxjg'] = $YwtypeModel->field('id,KP_Name,KP_Content')->where('KP_Type',2)->select();
        //顶部广告
        $PcgeneraladModel = new PcgeneraladModel;
        $index['dbgg'] = $PcgeneraladModel->field('id,KP_PicUrl,KP_Link')->where('KP_Type',9)->find();
        //热销楼盘
        $LpModel = new LpModel;             
        $index['tjlp'] = $LpModel->alias("a")
                         ->field('a.id,a.KP_LpName,a.KP_TaoJia,a.KP_City,a.KP_Wjt')
                         ->where($this->WhereCityA)
                         ->where("a.KP_Yhz",1)
                         ->order('a.KP_Cs desc ,a.KP_EditTime desc')
                         ->limit(8)
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
            $value['KP_Wjt'] = str_replace('Max', 'Cen', $value['KP_Wjt']);
            $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
        }

        if ($whereprice=="1=1") {
            $index['list'] = $LpModel
                        ->field('id,KP_Wjt,KP_LpName,KP_KfID,Kp_gflm,KP_City,KP_Tel,KP_Fjh,KP_Xszt,KP_Zlhx,KP_TsType,KP_Lpdz,KP_YouHui,KP_TaoJia,KP_Tj,KP_Yhz')
                        ->where($wheres)
                        ->where($wherec)
                        ->limit(10)
                        ->order('KP_Top desc , KP_EditTime desc')
                        ->select();
            $arr = array();
            foreach ($index['list'] as $key => $value) {
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
            foreach ($index['list'] as $key => $value) {
                if (isset($pricearr[$value['id']])) {
                    $value['KP_Qiprice'] = $pricearr[$value['id']]['KP_Qiprice'];
                    $value['KP_Juprice'] = $pricearr[$value['id']]['KP_Juprice'];
                    $value['KP_ValidityStart'] = $pricearr[$value['id']]['KP_ValidityStart'];
                    $value['KP_ValidityEnd'] = $pricearr[$value['id']]['KP_ValidityEnd'];
                }else{
                    $value['KP_Qiprice'] = 0;
                    $value['KP_Juprice'] = 0;
                    $value['KP_ValidityStart'] = 0;
                    $value['KP_ValidityEnd'] = 0;
                }
            }   
            $index['total'] = $LpModel
                        ->field('id')
                        ->where($wheres)
                        ->where($wherec)
                        ->count('id'); 

        }else{
            $index['list'] = $LpModel->alias('a')
                        ->join('lpprice b','b.KP_LpID=a.id')
                        ->field('a.id,a.KP_Wjt,a.KP_LpName,a.KP_KfID,a.Kp_gflm,a.KP_City,a.KP_Tel,a.KP_Fjh,a.KP_Xszt,a.KP_Wylx,a.KP_TsType,a.KP_Lpdz,a.KP_YouHui,a.KP_TaoJia,
                                 a.KP_Yhz,a.KP_Tj,a.KP_Zlhx,b.KP_Qiprice,b.KP_Juprice,b.KP_ValidityStart,b.KP_ValidityEnd')
                        ->where($wheres)
                        ->where($whereprice)
                        ->where($wherec)
                        ->order('a.KP_Top desc , a.KP_EditTime desc')
                        ->limit(10)->select();
            $index['total'] = $LpModel->alias('a')
                        ->join('lpprice b','b.KP_LpID=a.id')
                        ->field('a.id')
                        ->where($wheres)
                        ->where($wherec)
                        ->where($whereprice)
                        ->count('a.id');  
        }

                     
        $NewsModel = new NewsModel;          
        $GflmkfModel = new GflmkfModel;
        foreach ($index['list'] as $key => $value) {
            $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAME');
            $value['KP_Kefu'] = $GflmkfModel->where('id',$value['KP_KfID'])->value('KP_Pic');
            //$value['dtone'] = isset($dto[$value['id']][0])?$dto[$value['id']][0]:0;           
            $value['dtone'] = $NewsModel->field('id,KP_Title')->where('KP_LpID',$value['id'])->order('KP_EditTime desc')->limit(1)->select();
            if ($value['KP_ValidityStart']) {
                $value['KP_ValidityStart'] = date('Y年m月d日',strtotime($value['KP_ValidityStart']));
                $value['KP_ValidityEnd'] = date('Y年m月d日',strtotime($value['KP_ValidityEnd']));
            }
            $value['KP_Qiprice'] = floatval($value['KP_Qiprice']);
            $value['KP_Juprice'] = floatval($value['KP_Juprice']);
            $value['KP_Wjt'] = str_replace('Max', 'Cen', $value['KP_Wjt']);
        }                
        $this->view->assign("index",$index);
        return $this->view->fetch("index/house");
    }

    //新房数据接口
    public function indexapi(){
        $params = $this->request->post();
         
        $page = isset($params['page'])?($params['page']?$params['page']:1):1; 
        $pagesize = isset($params['pagesize'])?$params['pagesize']:10;
        $pages = ($page-1)*$pagesize;
        $area = isset($params['City'])?($params['City']?$params['City']:$this->City):$this->City;//区域
        $price = isset($params['Price'])?$params['Price']:0;//价格
        $priceorder = isset($params['Priceorder'])?$params['Priceorder']:0;//价格高低排序
        $tese = isset($params['Ts'])?$params['Ts']:'';//特色
        $sokey = isset($params['Sokey'])?$params['Sokey']:'';
        $Jjlp = isset($params['Jjlp'])?$params['Jjlp']:'';//优惠楼盘
        $hxjs = isset($params['Hx'])?$params['Hx']:'';//户型居室
        $wherestr = '1=1';
        $whereprice = '1=1';
        $order = 'a.KP_Top desc,a.KP_EditTime desc';
        if ($price) {
            $pricearr = explode('-', $price);
            $whereprice = 'b.KP_Qiprice > '.$pricearr[0].' and b.KP_Qiprice < '.$pricearr[1];
        }

        if ($priceorder) {
           $order = 'b.KP_Qiprice '.$priceorder;
           $whereprice .= ' and b.KP_Qiprice > 0';
        }

        if ($Jjlp) {
           $wherestr .= ' and KP_Jjlp = '.$Jjlp;
        }
        if ($sokey) {
            $wherestr .= ' and KP_LpName like "%'.$sokey.'%" or '.' KP_TsType like "%'.$sokey.'%" or '.' KP_Lpdz like "%'.$sokey.'%" or '.' KP_Kfs like "%'.$sokey.'%"';
        }
        if ($area) {
            $wherearea= 'KP_Provice = '.$area.' OR KP_Citys = '.$area.' OR KP_District = '.$area.' OR KP_County = '.$area;
        }else{
            $wherearea= '1=1';
        }

        $LpModel = new LpModel;
        $index['list'] = array();
        if ($price||$priceorder) {
            $index['list'] = $LpModel->alias('a')
                        ->join('lpprice b','b.KP_LpID=a.id')
                        ->field('a.id,a.KP_Wjt,a.KP_LpName,a.KP_KfID,a.Kp_gflm,a.KP_City,a.Kp_tel,a.KP_Fjh,a.KP_Xszt,a.KP_Wylx,a.KP_TsType,a.KP_Lpdz,a.KP_YouHui,a.KP_TaoJia,
                                 a.KP_Yhz,a.KP_Tj,a.KP_Zlhx,b.KP_Qiprice,b.KP_Juprice,b.KP_ValidityStart,b.KP_ValidityEnd')
                        ->where($wherearea)
                        ->where('KP_TsType','like','%'.$tese.'%')
                        ->where('KP_Zlhx','like','%'.$hxjs.'%')
                        ->where($wherestr)
                        ->where($whereprice)
                        ->order($order)
                        ->limit($pages,$pagesize)->select();
            $index['total'] = $LpModel->alias('a')
                        ->join('lpprice b','b.KP_LpID=a.id')
                        ->field('a.id')
                        ->where($wherearea)
                        ->where('a.KP_TsType','like','%'.$tese.'%')
                        ->where('KP_Zlhx','like','%'.$hxjs.'%')
                        ->where($whereprice)
                        ->where($wherestr)
                        ->count('a.id');            
        }else{
             $index['list'] = $LpModel->alias('a')
                        ->field('id,KP_Wjt,KP_LpName,KP_KfID,Kp_gflm,KP_City,Kp_tel,KP_Fjh,KP_Xszt,KP_Wylx,KP_TsType,KP_Lpdz,KP_YouHui,KP_TaoJia,KP_Tj,KP_Yhz,KP_Zlhx')
                        ->where($wherearea)
                        ->where('KP_TsType','like','%'.$tese.'%')
                        ->where('KP_Zlhx','like','%'.$hxjs.'%')
                        ->where($wherestr)
                        ->limit($pages,$pagesize)
                        ->order($order)
                        ->select();
            $arr = array();
            foreach ($index['list'] as $key => $value) {
                $arr[] = $value['id'];
            }      
            $LppriceModel = new LppriceModel; 
            $prices = $LppriceModel
                        ->field('KP_LpID,KP_Qiprice,KP_Juprice,KP_ValidityStart,KP_ValidityEnd')
                        ->where('KP_LpID','in',$arr)
                        ->select();
            $pricearr = array();            
            foreach ($prices as $key => $value) {
                $pricearr[$value['KP_LpID']] = $value;
            }
            foreach ($index['list'] as $key => $value) {
                if (isset($pricearr[$value['id']])) {
                    $value['KP_Qiprice'] = $pricearr[$value['id']]['KP_Qiprice'];
                    $value['KP_Juprice'] = $pricearr[$value['id']]['KP_Juprice'];
                    $value['KP_ValidityStart'] = $pricearr[$value['id']]['KP_ValidityStart'];
                    $value['KP_ValidityEnd'] = $pricearr[$value['id']]['KP_ValidityEnd'];
                }else{
                    $value['KP_Qiprice'] = 0;
                    $value['KP_Juprice'] = 0;
                    $value['KP_ValidityStart'] = 0;
                    $value['KP_ValidityEnd'] = 0;
                }
            }                      
            $index['total'] = $LpModel
                        ->field('id')
                        ->where($wherearea)
                       // ->where('KP_Wylx','like','%'.$wuye.'%')
                        ->where('KP_TsType','like','%'.$tese.'%')
                        ->where('KP_Zlhx','like','%'.$hxjs.'%')
                       // ->where('KP_Jzlx','like','%'.$jzlx.'%')
                        ->where($wherestr)
                        //->where($kptimewh)
                        ->count('id');    
        }
           
        $NewsModel = new NewsModel;          
        $GflmkfModel = new GflmkfModel;                
        foreach ($index['list'] as $key => $value) {
            $kfarr = explode(',', $value['KP_KfID']);
            if (count($kfarr)>0) {
                $value['KP_Kefu'] = $GflmkfModel->where('id',$kfarr[0])->value('KP_Pic');
            }else{
                $value['KP_Kefu'] = '';
            }
             $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAME');   
            $tl = $NewsModel->field('id,KP_Title')->where('KP_LpID',$value['id'])->order('KP_EditTime desc')->find();
            if ($tl) {
                $value['dtone'] = $tl; 
            }else{
                $value['dtone'] = 0; 
            }
            if ($value['KP_ValidityStart']) {
                $value['KP_ValidityStart'] = date('Y年m月d日',strtotime($value['KP_ValidityStart']));
                $value['KP_ValidityEnd'] = date('Y年m月d日',strtotime($value['KP_ValidityEnd']));
            }
            $value['KP_Qiprice'] = floatval($value['KP_Qiprice']);
            $value['KP_Juprice'] = floatval($value['KP_Juprice']);
            $value['KP_Wjt'] = str_replace('Max', 'Min', $value['KP_Wjt']);
        }
        $data = collection($index)->toArray();
        return json($data);
    }

    //获取时间范围
    function getNextMonthDays($date){
        $timestamp=strtotime($date);
        $arr=getdate($timestamp);
        if($arr['mon'] == 12){
            $year=$arr['year'] +1;
            $month=$arr['mon'] -11;
            $firstday=$year.'-0'.$month.'-01';
            $lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));
        }else{
            $firstday=date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)+1).'-01'));
            $lastday=date('Y-m-d',strtotime("$firstday +1 month -1 day"));
        }
        return array($firstday,$lastday);
    }

    //新房详细页
    public function house_details($ids = NULL){   
        //楼盘信息 
        $common = $this->LpInfo($ids);
        //预售许可证
        $YsxkzModel = new YsxkzModel;
        $ysxkz = $YsxkzModel->where('KP_LpID',$ids)->order('id desc')->value('KP_Name');
        if ($ysxkz) {
            $common['lpinfo']['KP_Ysxkz'] = $ysxkz;
        }
        
        $arr = explode(',',$common['lpinfo']['KP_Mapzb']);
        if ($common['lpinfo']['KP_Mapzb']) {
            $common['lpinfo']['longitude'] = $arr[0];
            $common['lpinfo']['latitude'] = $arr[1];
        }else{
            $common['lpinfo']['longitude'] = 0;
            $common['lpinfo']['latitude'] = 0;
        }
        $VideoModel = new VideoModel;
        $videoinfo = $VideoModel->field('KP_VideoUrl,KP_PicUrl')->where('KP_LpID',$ids)->find();
        if ($videoinfo) {
            $index['KP_VideoUrl'] = $videoinfo['KP_VideoUrl'];     
            $index['KP_VideoLogo'] = $videoinfo['KP_PicUrl']; 
        }else{
            $index['KP_VideoUrl'] = '';     
            $index['KP_VideoLogo'] = ''; 
        }
        $GflmkfModel = new GflmkfModel;   
        $kfarr = explode(',', $common['lpinfo']['KP_KfID']);
        if (count($kfarr)>0) {
            $index['lpinfo']['KP_Kefu'] =   $GflmkfModel->where('id',$kfarr[0])->value("KP_Pic");
        }else{
            $index['lpinfo']['KP_Kefu'] =   '';
        }
                                  
        //楼盘动态
        $NewsModel = new NewsModel;
        $index['lpnews'] = $NewsModel->field('id,KP_Title,KP_Lmid,KP_AddTime,KP_Description')
                                     ->where(['KP_LpID'=>$ids])
                                     ->limit(2)
                                     ->order('KP_Zd desc,id desc')
                                     ->select();
        foreach ($index['lpnews'] as $key => $value) {
            $value['KP_Typename'] = db('newslm')->where('id',$value['KP_Lmid'])->value('KP_Name');
            $value['KP_AddTime'] = date('Y-m-d',strtotime($value['KP_AddTime']));
        }
        //楼盘户型
        $LphxpicModel = new LphxpicModel;
        $index['lphx'] = $LphxpicModel->field('id,KP_Hx,KP_Area,KP_PicUrl,KP_HxTs,KP_Ckjg,KP_Content')->where('KP_LpID',$ids)->order('id desc')->limit(3)->select();
        //楼盘相册
        $LpalbumModel = new LpalbumModel;
        $LppicModel = new LppicModel;
        $index['lpalbum'] = $LpalbumModel->where("KP_LpID",$ids)->buildSql();
        $index['lpalbum'] = $LpalbumModel->table($index['lpalbum'])->alias('a')
                                         ->join('lppic b','a.id=b.KP_HcID')
                                         ->field('a.id,a.KP_LpID,a.KP_HcName,b.KP_PicUrl,b.KP_PicTitle,count(a.KP_HcName) as num')
                                         ->group('a.KP_HcName')
                                         ->select();
                                         
        $Lm = $LpalbumModel->getXcTypeList();
        foreach ($index['lpalbum'] as $key => $value) {
            $value['KP_PicTitle'] =  $Lm[$value['KP_HcName']];
            $value['KP_PicUrl'] = str_replace('Max', 'Min', $value['KP_PicUrl']);
        }
         
        $index['lpalbumpic'] = $LppicModel->field('id,KP_PicUrl')->limit(4)->select();

        //print_r(collection($index['lpalbum'])->toArray());exit;   
        //项目价格走势
        $LppriceModel = new LppriceModel;
        $index['xmjgzs'] = $LppriceModel->where('KP_LpID',$ids)->order('KP_JgTime desc')->limit(1)->select();
        foreach ($index['xmjgzs'] as $k => $v) {
            $v['KP_Qiprice'] = floatval($v['KP_Qiprice']);
            $v['KP_Juprice'] = floatval($v['KP_Juprice']);
            $v['KP_ValidityStart'] = date("Y年m月d日",strtotime($v['KP_ValidityStart']));
            $v['KP_ValidityEnd'] = date("Y年m月d日",strtotime($v['KP_ValidityEnd']));
        }
        if(count($index['xmjgzs'])<1){
            $index['xmjgzs'][0]['KP_Qiprice'] = '';
            $index['xmjgzs'][0]['KP_Juprice'] = '';
            $index['xmjgzs'][0]['KP_ValidityEnd'] = '';
            $index['xmjgzs'][0]['KP_ValidityStart'] = ''; 
            if ($common['lpinfo']['KP_TaoJia']) {
                $index['BdTime'] = floor((strtotime(date('Y-m-d'))-strtotime(date('Y-m-d',strtotime($common['lpinfo']['KP_EditTime']))))/86400);
            }else{
                $index['BdTime'] = 0;
            }
        }else{
            $index['BdTime'] = floor((strtotime(date('Y-m-d'))-strtotime(date('Y-m-d',strtotime($index['xmjgzs'][0]['KP_EditTime']))))/86400);
        }
        $index['KP_Price'] = $index['xmjgzs'][0]['KP_Juprice']?$index['xmjgzs'][0]['KP_Juprice'].'元/㎡':($common['lpinfo']['KP_TaoJia']?$common['lpinfo']['KP_TaoJia'].'万元/套':'待定');
        $LpModel = new LpModel;
        //为你推荐
        $index['tjlp'] = $LpModel->alias("a")
                         ->field('a.id,a.KP_Wjt,a.KP_LpName,a.KP_TaoJia,a.KP_City,a.KP_Provice')
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
            $value['KP_Price'] = $value['KP_Juprice']?$value['KP_Juprice'].'元/㎡':($value['KP_TaoJia']?$value['KP_TaoJia'].'万元/套':'待定');
        } 

        //周边楼盘
        $index['zblp'] = $LpModel->alias("a")
                         ->field('a.id,a.KP_Wjt,a.KP_LpName,a.KP_TaoJia,a.KP_City')
                         ->where('KP_City',$common['lpinfo']['KP_City'])
                         ->where('KP_Xszt','in',[0,1])
                         ->where('a.id','<>',$ids)
                         ->order('a.KP_EditTime desc,a.KP_Cs')
                         ->limit(10)
                         ->select();

        $arr = array();
        foreach ($index['zblp'] as $key => $value) {
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
        foreach ($index['zblp'] as $key => $value) {
            if (isset($pricearr[$value['id']])) {
                $value['KP_Qiprice'] = floatval($pricearr[$value['id']]['KP_Qiprice']);
                $value['KP_Juprice'] = floatval($pricearr[$value['id']]['KP_Juprice']);
            }else{
                $value['KP_Qiprice'] = 0;
                $value['KP_Juprice'] = 0;
            }
            $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
            $value['KP_Wjt'] = str_replace('Max', 'Min', $value['KP_Wjt']);
            $value['KP_Price'] = $value['KP_Juprice']?$value['KP_Juprice'].'元/㎡':($value['KP_TaoJia']?$value['KP_TaoJia'].'万元/套':'待定');
        }   

        //同价位楼盘
        
        if ($common['lpinfo']['KP_Juprice']) {
            $whereprice = "b.KP_Juprice > ".($common['lpinfo']['KP_Juprice_int']-5000).' and b.KP_Juprice < '.($common['lpinfo']['KP_Juprice_int']+5000);
            $index['tjwlp'] = $LpModel->alias('a')
                        ->join('lpprice b','b.KP_LpID=a.id')
                        ->field('a.id,a.KP_LpName,a.KP_City,a.KP_TaoJia,b.KP_Qiprice,b.KP_Juprice')
                        ->where('a.KP_Provice',$common['lpinfo']['KP_Provice'])
                        ->where($whereprice)
                        ->where('a.id','<>',$ids)
                        ->order('a.KP_EditTime desc')
                        ->limit(10)->select();
            foreach ($index['tjwlp'] as $key => $value) {
                $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
                $value['KP_Price'] = $value['KP_Juprice']?floatval($value['KP_Juprice']).'元/㎡':($value['KP_TaoJia']?floatval($value['KP_TaoJia']).'万元/套':'待定');
            }               
        }else if($common['lpinfo']['KP_TaoJia']){
            $whereprice = "KP_TaoJia > ".($common['lpinfo']['KP_TaoJia']-100).' and KP_TaoJia < '.($common['lpinfo']['KP_TaoJia']+100);
            $index['tjwlp'] = $LpModel
                        ->field('id,KP_LpName,KP_City,KP_TaoJia')
                        ->where($whereprice)
                        ->where('KP_Provice',$common['lpinfo']['KP_Provice'])
                        ->where('id','<>',$ids)
                        ->where('KP_TaoJia','>',0)
                        ->order('KP_EditTime desc')
                        ->limit(10)->select();
            foreach ($index['tjwlp'] as $key => $value) {
                $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
                $value['KP_Price'] = floatval($value['KP_TaoJia']).'万元/套';
            }            
        }else{
            $index['tjwlp'] = $LpModel->alias('a')
                        ->join('lpprice b','b.KP_LpID=a.id')
                        ->field('a.id,a.KP_LpName,a.KP_City,a.KP_TaoJia,b.KP_Qiprice,b.KP_Juprice')
                        ->where('a.id','<>',$ids)
                        ->where('a.KP_Provice',$common['lpinfo']['KP_Provice'])
                        ->order('a.KP_EditTime desc')
                        ->limit(10)->select();
            foreach ($index['tjwlp'] as $key => $value) {
                $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
                $value['KP_Price'] = $value['KP_Juprice']?floatval($value['KP_Juprice']).'元/㎡':($value['KP_TaoJia']?floatval($value['KP_TaoJia']).'万元/套':'待定');
            }              
        }     

        //热销楼盘
        $index['rxlp'] = $LpModel->alias("a")
                         ->field('a.id,a.KP_Wjt,a.KP_LpName,a.KP_TaoJia,a.KP_City')
                         ->where('a.KP_Provice',$common['lpinfo']['KP_Provice'])
                         ->where('a.id','<>',$ids)
                         ->where("a.KP_Yhz",1)
                         ->where('KP_Xszt','in',[0,1])
                         ->order('a.KP_EditTime desc')
                         ->limit(10)
                         ->select();

        $arr = array();
        foreach ($index['rxlp'] as $key => $value) {
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
        foreach ($index['rxlp'] as $key => $value) {
            if (isset($pricearr[$value['id']])) {
                $value['KP_Qiprice'] = floatval($pricearr[$value['id']]['KP_Qiprice']);
                $value['KP_Juprice'] = floatval($pricearr[$value['id']]['KP_Juprice']);
            }else{
                $value['KP_Qiprice'] = 0;
                $value['KP_Juprice'] = 0;
            }
            $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
            $value['KP_Wjt'] = str_replace('Max', 'Min', $value['KP_Wjt']);
            $value['KP_Price'] = $value['KP_Juprice']?$value['KP_Juprice'].'元/㎡':($value['KP_TaoJia']?$value['KP_TaoJia'].'万元/套':'待定');
        }   
             
        //print_r(collection($index['lpnews'])->toArray());exit;   
        //更新浏览次数
        $LpModel->where('id', $ids)->setInc('KP_Cs');                         
        $this->view->assign('index',array_merge($index, $common));
        return $this->view->fetch("index/house_details");
    }

    public function house_details_zblpajax(){
        $params = $this->request->post();
        $ids = isset($params['lpid'])?$params['lpid']:-1;
        $lpcity = isset($params['lpcity'])?$params['lpcity']:-1;
        //周边楼盘
        $LpModel = new LpModel;
        $index['zblp'] = $LpModel->alias("a")
                         ->field('a.id,a.KP_Wjt,a.KP_LpName,a.KP_TaoJia,a.KP_City')
                         ->where('KP_Provice',$lpcity)
                         ->where('a.KP_Yhz',1)
                         ->where('a.id','<>',$ids)
                         ->order('a.KP_EditTime desc,a.KP_Cs')
                         ->limit(3)
                         ->select();

        $arr = array();
        foreach ($index['zblp'] as $key => $value) {
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
        foreach ($index['zblp'] as $key => $value) {
            if (isset($pricearr[$value['id']])) {
                $value['KP_Qiprice'] = floatval($pricearr[$value['id']]['KP_Qiprice']);
                $value['KP_Juprice'] = floatval($pricearr[$value['id']]['KP_Juprice']);
            }else{
                $value['KP_Qiprice'] = 0;
                $value['KP_Juprice'] = 0;
            }
            $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
            $value['KP_Wjt'] = str_replace('Max', 'Min', $value['KP_Wjt']);
            $value['KP_Price'] = $value['KP_Juprice']?$value['KP_Juprice']:($value['KP_TaoJia']?$value['KP_TaoJia']:'待定');
            $value['KP_Price_unit'] = $value['KP_Juprice']?'元/㎡':($value['KP_TaoJia']?'万元/套':'');
            $value['KP_Price_title'] = $value['KP_Juprice']?'均价：':($value['KP_TaoJia']?'套价：':'');
        } 
        return json(collection($index)->toArray());
    }

    public function house_details_xcajax(){
        $params = $this->request->post();
        $types = isset($params['types'])?$params['types']:-1;
        $ids = isset($params['lpid'])?$params['lpid']:-1;
        $LpalbumModel = new LpalbumModel;
        $where = '1=1';
        if ($types!=-1) {
            $where .= ' and a.KP_HcName = '.$types;
        }
        $index['rows'] = $LpalbumModel->alias("a")
                                      ->join('lppic b','a.id=b.KP_HcID')
                                      ->field('a.id,a.KP_LpID,a.KP_HcName,b.id as bid,b.KP_PicTitle,b.KP_PicUrl')
                                      ->where(['a.KP_LpID'=>$ids])
                                      ->where($where)
                                      ->limit(4)
                                      ->order('a.id desc')
                                      ->select();
        foreach ($index['rows'] as $key => $value) {
            $value['KP_PicUrl'] = str_replace('Max', 'Min', $value['KP_PicUrl']);
        }
        return json(collection($index)->toArray());
    }

     public function zhekou($City=""){

        //热销楼盘
        $LpModel = new LpModel;
        $NewsModel = new NewsModel;
        if ($City!="") {
            $this->WhereCityA = '(a.KP_Provice = '.$City.' or a.KP_Citys = '.$City.' or a.KP_District = '.$City.' or a.KP_County = '.$City.')';
        }

        //阅房旅居推荐楼盘
        $zklp = $LpModel->alias("a")
                         ->field('a.id,a.KP_Wjt,a.KP_LpName,a.KP_Tel,a.KP_Lpdz,a.KP_Fjh,a.KP_TaoJia,a.KP_City')
                         ->where($this->WhereCityA)
                         ->where("a.KP_Jjlp",1)
                         ->order('a.KP_Cs desc ,a.KP_EditTime desc')
                         ->limit(10)
                         ->buildSql();
        $index['zklp'] = $LpModel->table($zklp)->alias('a')
                        ->join('lpprice b','b.KP_LpID=a.id',"left")
                        ->field('a.id,a.KP_Wjt,a.KP_LpName,a.KP_Tel,a.KP_Lpdz,a.KP_Fjh,a.KP_TaoJia,a.KP_City,b.KP_Qiprice,b.KP_Juprice')
                        ->select();                 

        foreach ($index['zklp'] as $k => $v) {
            $v['KP_Qiprice'] = floatval($v['KP_Qiprice']);
            $v['KP_Juprice'] = floatval($v['KP_Juprice']);
            $v['CNAME'] = db('zhcity')->where('id',$v['KP_City'])->cache()->value('CNAMES');
            $v['KP_Description'] = $NewsModel->where(['KP_LpID'=>$v['id']])->order('id desc')->value('KP_Description');
        }

        //区域
        $AreaModel = new AreaModel;
        $index['qy'] = $AreaModel->field('id,name,areaid') 
                                   ->where($this->WhereCity)
                                   ->where("areapid","<>",0)
                                   ->order('weigh Desc')
                                   ->select(); 
        $index['city'] =  $City     ;
        $this->view->assign('index', $index );
        return $this->view->fetch("index/housezhekou");
    }

    //价格走势
    public function pricezs(){
        $params = $this->request->post();

        //项目价格走势
        $LppriceModel = new LppriceModel;
        $xmjgzs = $LppriceModel->field("KP_Qiprice,KP_Juprice,left(KP_JgTime, 7) as KP_JgTime")
                               ->where('KP_LpID',$params['id'])
                               ->order('KP_JgTime asc')
                               ->select();
        $qijia = array();
        $qijias = array();
        $jujia = array();
        $jujias = array();
        $taojia = array();
        $qflag = isset($xmjgzs[0]['KP_Qiprice'])?floatval($xmjgzs[0]['KP_Qiprice']):0;
        $jflag = isset($xmjgzs[0]['KP_Juprice'])?floatval($xmjgzs[0]['KP_Juprice']):0;
        foreach ($xmjgzs as $k => $v) {
            $v['KP_Qiprice'] = floatval($v['KP_Qiprice']);
            $v['KP_Juprice'] = floatval($v['KP_Juprice']);
            $qijia[$v['KP_JgTime']]['KP_Qiprice'] = $v['KP_Qiprice'];
            $jujia[$v['KP_JgTime']]['KP_Juprice'] = $v['KP_Juprice'];
        }

        $month = array(); 
        for ($i=7; $i >= 0; $i--) {
            $month[] =  $this->GetMonth($i,1);
            $t = $this->GetMonth($i,0);
            if (isset($qijia[$t])) {
                $qijias[] = $qijia[$t]['KP_Qiprice'];
                $qflag = $qijia[$t]['KP_Qiprice'];
            }else{
                $qijias[] = $qflag;
            }
            if (isset($jujia[$t])) {
                $jujias[] = $jujia[$t]['KP_Juprice'];
                $jflag = $jujia[$t]['KP_Juprice'];
            }else{
                $jujias[] = $jflag;
            }
            $taojia[] = floatval($params['taojia']);
        }
        $index['month'] = $month;
        $index['KP_Juprice'] = $jujias;
        $index['KP_Qiprice'] = $qijias;
        $index['KP_TaoJia'] = $taojia;
        if (count($xmjgzs)<2&&$xmjgzs[0]['KP_Qiprice']<1) {
            $index['flag'] = 0;
        }else{
            $index['flag'] = 1;
        }
        return json(collection($index)->toArray());
    }

    //获取当前月份的前一月
    function GetMonth($sign,$f=1){  
        //得到系统的年月  
        $tmp_date=date("Ym");  
        //切割出年份  
        $tmp_year=substr($tmp_date,0,4);  
        //切割出月份  
        $tmp_mon =substr($tmp_date,4,2);  
        // 得到当前月份的下几月
        $tmp_nextmonth=mktime(0,0,0,$tmp_mon+$sign,1,$tmp_year);  
        // 得到当前月份的前几月
        $tmp_forwardmonth=mktime(0,0,0,$tmp_mon-$sign,1,$tmp_year);  
        if ($f==1) {
           return $fm_next_month=date("Y年m月",$tmp_forwardmonth);
        }else{
           return $fm_next_month=date("Y-m",$tmp_forwardmonth);
        }
    }

    //详细信息
    public function lpdetail($ids=''){
        //楼盘信息 
        $common = $this->LpInfo($ids);
        $LpcontentModel = new LpcontentModel;
        $index['xmjs'] = $LpcontentModel->where('KP_Lm',0)->where('KP_LpID',$ids)->order('id desc')->find();
        $index['zbpt'] = $LpcontentModel->where('KP_Lm',1)->where('KP_LpID',$ids)->order('id desc')->find();
        $LppriceModel = new LppriceModel; 
        $prices = $LppriceModel
                    ->field('KP_Juprice,KP_ValidityEnd')
                    ->where('KP_LpID',$ids)
                    ->find();
        if ($prices) {
            $index['KP_Price'] = floatval($prices['KP_Juprice']).'元/㎡';
            $index['KP_DqTime'] ='（有效期至'.date('Y/m/d',strtotime($prices['KP_ValidityEnd'])).'）';
        }else{
            $index['KP_Price'] = $common['lpinfo']['KP_TaoJia']?floatval($common['lpinfo']['KP_TaoJia']).'万元/套':'待定';
            $index['KP_DqTime'] = '（套价）';
        }

        //预售许可证
        $YsxkzModel = new YsxkzModel;
        $index['ysxkz'] = $YsxkzModel->where('KP_LpID',$ids)->order('id desc')->select();
        //为你推荐
        $LpModel = new LpModel;
        $index['wntj'] = $LpModel->alias("a")
                         ->field('a.id,a.KP_Wjt,a.KP_LpName,a.KP_TaoJia,a.KP_City')
                         ->where($this->WhereCityA)
                         ->where("a.KP_Tj",1)
                         ->where('KP_Xszt','in',[0,1])
                         ->order('a.KP_Cs desc ,a.KP_EditTime desc')
                         ->limit(4)
                         ->select();

        $arr = array();
        foreach ($index['wntj'] as $key => $value) {
            $arr[] = $value['id'];
        }      
        
        $prices = $LppriceModel
                    ->field('KP_LpID,KP_Qiprice,KP_Juprice')
                    ->where('KP_LpID','in',$arr)
                    ->order('id asc')
                    ->select();
        $pricearr = array();            
        foreach ($prices as $key => $value) {
            $pricearr[$value['KP_LpID']] = $value;
        }
        foreach ($index['wntj'] as $key => $value) {
            if (isset($pricearr[$value['id']])) {
                $value['KP_Qiprice'] = floatval($pricearr[$value['id']]['KP_Qiprice']);
                $value['KP_Juprice'] = floatval($pricearr[$value['id']]['KP_Juprice']);
            }else{
                $value['KP_Qiprice'] = 0;
                $value['KP_Juprice'] = 0;
            }
            $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
            $value['KP_Wjt'] = str_replace('Max', 'Cen', $value['KP_Wjt']);
            $value['KP_Price'] = $value['KP_Juprice']?$value['KP_Juprice'].'元/㎡':($value['KP_TaoJia']?$value['KP_TaoJia'].'万元/套':'待定');
        }    
        $this->view->assign('index',array_merge($index, $common));
        return $this->view->fetch("index/details");
    }

    //楼盘户型图index 
    public function lphx($ids=''){
        //楼盘信息 
        $common = $this->LpInfo($ids);
        $LphxpicModel = new LphxpicModel;
        $hxarr = LphxpicModel::getHxTypeList();
        $index['Tyeps'] = $LphxpicModel->field('count(KP_Type) as num,KP_Type')->where('KP_LpID',$ids)->group('KP_Type')->select();
        foreach ($index['Tyeps'] as $key => $value) {
            $value['name'] = $hxarr[$value['KP_Type']];
        }
        $index['rows'] = $LphxpicModel->field('id, KP_LpID, KP_Type, KP_Hxbh, KP_Xszt, KP_Hx,KP_HxTs, KP_Bslc, KP_Area, KP_PicUrl, KP_Ckjg, KP_Cx,KP_Content')
                                      ->where('KP_LpID',$ids)->order('id desc')->select();
        $index['total'] = $LphxpicModel->where(['KP_LpID'=>$ids])->count();
        /*foreach ($index['rows'] as $key => $value) {        
            $value['KP_PicUrl'] = str_replace('Max', 'Cen', $value['KP_PicUrl']);
        }*/   
        //return json(array_merge($index, $common));
        $this->view->assign('index',array_merge($index, $common));
        return $this->view->fetch("index/house_huxing");
    }

    //户型detail
    public function huxing_detail($ids='',$hxid=''){
        //楼盘信息 
        $arr = array(0=>'在售' ,1=>'热销',2=>'售罄',3=>'代售' );
        $common = $this->LpInfo($ids);
        $LphxpicModel = new LphxpicModel;
        $hxarr = LphxpicModel::getHxTypeList();
        $index['Tyeps'] = $LphxpicModel->field('count(KP_Type) as num,KP_Type')->where('KP_LpID',$ids)->group('KP_Type')->select();
        foreach ($index['Tyeps'] as $key => $value) {
            $value['name'] = $hxarr[$value['KP_Type']];
        }
        $index['rows'] = $LphxpicModel->field('id, KP_LpID, KP_Type, KP_Hxbh, KP_Xszt, KP_Hx,KP_HxTs, KP_Bslc, KP_Area, KP_PicUrl, KP_Ckjg, KP_Cx,KP_Content')
                                      ->where('KP_LpID',$ids)->order('id desc')->select();
        $index['total'] = $LphxpicModel->where(['KP_LpID'=>$ids])->count();
        foreach ($index['rows'] as $key => $value) {        
            $value['KP_TypeName'] = $arr[$value['KP_Xszt']];
        }   
        $index['selectid'] = $hxid;
        //return json(array_merge($index, $common));
        $this->view->assign('index',array_merge($index, $common));
        return $this->view->fetch("index/house_huxing_detail");
    }

    //楼盘户型图
    public function lphxajax($ids=''){
        //楼盘户型
        $params = $this->request->post();
        $page = isset($params['page'])?$params['page']:0;
        $pagesize = isset($params['pagesize'])?$params['pagesize']:6;
        $types = isset($params['types'])?$params['types']:-1;
        $id = isset($params['id'])?$params['id']:0;
        $LphxpicModel = new LphxpicModel;
        $where = '1=1';
        if ($types!=-1) {
            $where .= ' and KP_Type = '.$types;
        }
        $index['total'] = $LphxpicModel->where(['KP_LpID'=>$id])->where($where)->count();
        $index['rows'] = $LphxpicModel->where('KP_LpID',$id)->where($where)->order('id desc')->limit($page,$pagesize)->select();
        return json(collection($index)->toArray());
    }

    //楼盘相册index
    public function lpxc($ids=''){
        //楼盘信息 
        $common = $this->LpInfo($ids);
        $LpalbumModel = new LpalbumModel;
        $arrs = LpalbumModel::getXcTypeList();
        $index['total'] = $LpalbumModel->alias("a")
                                       ->join('lppic b','a.id=b.KP_HcID')
                                       ->where(['a.KP_LpID'=>$ids])
                                       ->count();
        $index['temp'] =  $LpalbumModel->alias("a")
                                       ->join('lppic b','a.id=b.KP_HcID')
                                       ->field('a.id,a.KP_LpID,a.KP_HcName,b.id as bid,b.KP_PicTitle,b.KP_PicUrl')
                                       ->where(['a.KP_LpID'=>$ids])
                                       ->order('a.id desc')
                                       ->select();
        $index['ttemp'] = $LpalbumModel->alias("a")
                                       ->join('lppic b','a.id=b.KP_HcID')
                                       ->field('count(a.KP_HcName) as num,a.KP_HcName,b.KP_PicTitle')
                                       ->where(['a.KP_LpID'=>$ids])
                                       ->group('a.KP_HcName')
                                       ->select();
        /*foreach ($index['Types'] as $key => $value) {
            $value['KP_PicTitle'] = $arrs[$value['KP_HcName']];
        }*/
        $b = array();
        foreach ($index['ttemp'] as $key => $value) {
            $value['KP_PicTitle'] = $arrs[$value['KP_HcName']];
            $b[$value['KP_HcName']][] = $value;
        }
        //510243
        $a = array();
        foreach ($index['temp'] as $key => $value) {
            $a[$value['KP_HcName']][] = $value;
        }
        $index['rows'] =array();
        $index['Types'] =array();
        if (isset($a[5])) {
            foreach ($a[5] as $key => $value) {
                array_push($index['rows'],$value);
            }
            foreach ($b[5] as $key => $value) {
                array_push($index['Types'],$value);
            }
        }
        if (isset($a[1])) {
            foreach ($a[1] as $key => $value) {
                array_push($index['rows'],$value);
            }
            foreach ($b[1] as $key => $value) {
                array_push($index['Types'],$value);
            }
        }
        if (isset($a[0])) {
            foreach ($a[0] as $key => $value) {
                array_push($index['rows'],$value);
            }
            foreach ($b[0] as $key => $value) {
                array_push($index['Types'],$value);
            }
        }
        if (isset($a[2])) {
            foreach ($a[2] as $key => $value) {
                array_push($index['rows'],$value);
            }
            foreach ($b[2] as $key => $value) {
                array_push($index['Types'],$value);
            }
        }
        if (isset($a[4])) {
            foreach ($a[4] as $key => $value) {
                array_push($index['rows'],$value);
            }
            foreach ($b[4] as $key => $value) {
                array_push($index['Types'],$value);
            }
        }
        if (isset($a[3])) {
            foreach ($a[3] as $key => $value) {
                array_push($index['rows'],$value);
            }
            foreach ($b[3] as $key => $value) {
                array_push($index['Types'],$value);
            }
        }
        $this->view->assign('index',array_merge($index, $common));
        return $this->view->fetch("index/house_xiangce");
    }

    //楼盘相册
    public function lpxcajax($ids=''){
        $params = $this->request->post();
        $page = isset($params['page'])?$params['page']:0;
        $pagesize = isset($params['pagesize'])?$params['pagesize']:6;
        $types = isset($params['types'])?$params['types']:-1;
        $LpalbumModel = new LpalbumModel;
        $where = '1=1';
        if ($types!=-1) {
            $where .= ' and a.KP_HcName = '.$types;
        }
        $index['total'] = $LpalbumModel->alias("a")
                                       ->join('lppic b','a.id=b.KP_HcID')
                                       ->where(['a.KP_LpID'=>$ids])
                                       ->where($where)
                                       ->count();
        $index['rows'] = $LpalbumModel->alias("a")
                                      ->join('lppic b','a.id=b.KP_HcID')
                                      ->field('a.id,a.KP_LpID,a.KP_HcName,b.id as bid,b.KP_PicTitle,b.KP_PicUrl')
                                      ->where(['a.KP_LpID'=>$ids])
                                      ->where($where)
                                      ->limit($page,$pagesize)
                                      ->order('a.id desc')
                                      ->select();

        return json(collection($index)->toArray());
    }

    //楼盘动态
    public function lpdt($ids=''){
        //楼盘信息 
        $common = $this->LpInfo($ids);
        $NewsModel = new NewsModel;
        $index['lpdt'] = $NewsModel->field('id,KP_PicUrl,KP_Cs,KP_Title,KP_Description,KP_AddTime')->where(['KP_LpID'=>$ids])->order('KP_Zd desc,id desc')->select();
        foreach ($index['lpdt'] as $key => $value) {
            $value['KP_Time'] = date('Y-m',strtotime($value['KP_AddTime']));
            $value['KP_Day'] = date('d',strtotime($value['KP_AddTime']));
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
              $value['KP_Price'] = $value['KP_Juprice']?$value['KP_Juprice'].'元/㎡':($value['KP_TaoJia']?$value['KP_TaoJia'].'万元/套':'待定');
          }  
        $this->view->assign('index',array_merge($index, $common));
        return $this->view->fetch("index/house_dongtai");
    }

    //点评页
    public function lpdp($ids=''){
        //楼盘信息 
        $common = $this->LpInfo($ids);
          //楼盘问答
        $LpreviewModel = new LpreviewModel;
        $index['lpwd'] = $LpreviewModel->where(['KP_LpID'=>$ids,'KP_Check'=>1])->select();
        $index['lpwdnum'] = $LpreviewModel->field('id')->where(['KP_LpID'=>$ids,'KP_Check'=>1])->count('id');
        foreach ($index['lpwd'] as $key => $value) {
            $value['KP_Phone'] = substr_replace($value['KP_Phone'],'****',3,4);
        }

        //print_r(collection($index)->toArray());exit;
        $this->view->assign('index',array_merge($index, $common));
        return $this->view->fetch("index/house_dianping");
    }

   

    //楼盘动态、楼盘点评、楼盘问答 左边公用块
    public function Leftcommon($ids='',$KP_City='',$index){
        //楼盘信息 
        $LptgbmModel = new LptgbmModel;
        $LpreviewModel = new LpreviewModel;
        $LpcommentModel = new LpcommentModel;
        //问答条数
        $index['wdts'] = $LpreviewModel->where('KP_LpID',$ids)->count();
        //点评条数
        $index['dpts'] = $LpcommentModel->where('KP_LpID',$ids)->count();
        //报名人数
        $index['bmrs'] = $LptgbmModel->where('KP_LpID',$ids)->count();
        //周边楼盘
        $where = '(a.KP_Provice = '.$KP_City.' or a.KP_Citys = '.$KP_City.' or a.KP_District = '.$KP_City.' or a.KP_County = '.$KP_City.')';
        //阅房旅居推荐楼盘
        $LpModel = new LpModel;
        $index['zblp'] = $LpModel->alias("a")
                         ->field('a.id,a.KP_LpName,a.KP_TaoJia,a.KP_City')
                         ->where($where)
                         ->order('a.KP_Cs desc ,a.KP_EditTime desc')
                         ->limit(9)
                         ->select();

        $arr = array();
        foreach ($index['zblp'] as $key => $value) {
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
        foreach ($index['zblp'] as $key => $value) {
            if (isset($pricearr[$value['id']])) {
                $value['KP_Qiprice'] = floatval($pricearr[$value['id']]['KP_Qiprice']);
                $value['KP_Juprice'] = floatval($pricearr[$value['id']]['KP_Juprice']);
            }else{
                $value['KP_Qiprice'] = 0;
                $value['KP_Juprice'] = 0;
            }
            $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
        }               

        $index['wygz'] = $LpModel->alias("a")
                         ->field('a.id,a.KP_LpName,a.KP_TaoJia,a.KP_City')
                         ->where($this->WhereCityA)
                         ->order('a.KP_Zycs' ,'desc')
                         ->limit(9)
                         ->select();

        $arr = array();
        foreach ($index['wygz'] as $key => $value) {
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
        foreach ($index['wygz'] as $key => $value) {
            if (isset($pricearr[$value['id']])) {
                $value['KP_Qiprice'] = floatval($pricearr[$value['id']]['KP_Qiprice']);
                $value['KP_Juprice'] = floatval($pricearr[$value['id']]['KP_Juprice']);
            }else{
                $value['KP_Qiprice'] = 0;
                $value['KP_Juprice'] = 0;
            }
            $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
        }                   

        return $index;
    }
   
    //获取楼盘信息
    public function LpInfo($ids){
        $LpModel = new LpModel;
        $lp = $LpModel->get($ids);
        $lp['KP_KpTime'] = $lp['KP_KpTime']?date("Y年m月d日",strtotime($lp['KP_KpTime'])):'';
        $lp['KP_RzTime'] = $lp['KP_RzTime']?date("Y年m月d日",strtotime($lp['KP_RzTime'])):'';
        $lp['CNAME'] = Zhcity::where('id',$lp['KP_City'])->value('CNAME');
        $lp['QNAME'] = Zhcity::where('id',$lp['KP_Provice'])->value('CNAME').' '.Zhcity::where('id',$lp['KP_Citys'])->value('CNAME')
                       .' '.Zhcity::where('id',$lp['KP_District'])->value('CNAME').' '.Zhcity::where('id',$lp['KP_County'])->value('CNAME');
        $zx = LpModel::getTypeList();
        $lp['KP_Zxqk'] = $zx[$lp['KP_Zxqk']];
        $LpactivityModel = new LpactivityModel;
        $lpyh = $LpactivityModel->where("KP_LpID",$ids)->value("KP_Info");
        if ($lpyh) {
            $lp['KP_Activity'] = $lpyh;
        }else{
            $lp['KP_Activity'] = $lp['KP_YouHui'];
        }
        $LppriceModel = new LppriceModel;
        $prices = $LppriceModel
                    ->field('KP_Juprice,KP_ValidityEnd')
                    ->where('KP_LpID',$ids)
                    ->order('id desc')
                    ->find();
        if ($prices['KP_Juprice']>0) {
            $lp['KP_Juprice'] = floatval($prices['KP_Juprice']).'元/㎡';
            $lp['KP_Juprice_int'] = floatval($prices['KP_Juprice']);
        }else{
            $lp['KP_Juprice'] = 0;
        }

        $index['lpinfo'] = $lp;
        $this->view->assign('lpname',$lp['KP_LpName'].'详情-');
        return $index;
    }

    //楼盘点评 KP_LpID KP_Score KP_Phone KP_Content KP_Time KP_Agree KP_Disapprove KP_Check KP_KhName
    //sType: 'Add',T_LpID: '<%=Tid%>',T_Title: '<%=T_LpName%>',C_Score: C_Score,C_Phone: C_Phone,C_Content: C_Content
    public function comment(){
        $LpcommentModel = new LpcommentModel;
        $params = $this->request->post();
        if ($params) {
            try {
                $data['KP_LpID'] = $params['T_LpID'];
                $data['KP_Score'] = $params['C_Score'];
                $data['KP_Phone'] = $params['C_Phone'];
                $data['KP_Content'] = $params['C_Content'];
                $data['KP_KhName'] = '游客';
                $data['KP_Time'] = date("Y-m-d H:i:s");
                $result =  $LpcommentModel->allowField(true)->save($data);
                if ($result !== false) {
                    return json(array('info'=>'Yes'));
                } else {
                    return json(array('info'=>$LpreviewModel->getError()));
                }
            } catch (\think\exception\PDOException $e) {
                return json(array('info'=>$e->getMessage()));
            } catch (\think\Exception $e) {
                return json(array('info'=>$e->getMessage()));
            }
        }
    }

    //楼盘问答 KP_LpID KP_Nickname KP_Phone KP_Content KP_PLTime KP_HfContent KP_IP KP_Check KP_FlagHf
    //sType: 'Add',T_LpID: '<%=Tid%>',Ask_Phone: Ask_Phone,T_Title: T_Title,Ask_Content: Ask_Content
    public function wenda(){
        $LpreviewModel = new LpreviewModel;
        $params = $this->request->post();
        if ($params) {
            try {
                $request = Request::instance();
                $data['KP_LpID'] = $params['T_LpID'];
                $data['KP_Phone'] = $params['Ask_Phone'];
                $data['KP_Nickname'] = '游客';
                $data['KP_Content'] = $params['Ask_Content'];
                $data['KP_IP'] = $request->ip();
                $data['KP_PLTime'] = date("Y-m-d H:i:s");
                $result =  $LpreviewModel->allowField(true)->save($data);
                if ($result !== false) {
                    return json(array('info'=>'Yes'));
                } else {
                    return json(array('info'=>$LpreviewModel->getError()));
                }
            } catch (\think\exception\PDOException $e) {
                return json(array('info'=>$e->getMessage()));
            } catch (\think\Exception $e) {
                return json(array('info'=>$e->getMessage()));
            }
        }
    }

    //经纬度范围
    public function returnSquarePoint($lng, $lat,$distance = 50){
        $earthdata=6371;//地球半径，平均半径为6371km
        $dlng = 2 * asin(sin($distance / (2 * $earthdata)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);
        $dlat = $distance/$earthdata;
        $dlat = rad2deg($dlat);
        $arr=array(
            'left_top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
            'right_top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
            'left_bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
            'right_bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
        );
        return $arr;
    }

    //喜欢楼盘
    public function like(){
        $params = $this->request->post();
        $LpModel = new LpModel;
        $info = $LpModel->where('id', $params['id'])->setInc('KP_Zycs');
        if ($info) {
            return json(array('status'=>1,'info'=>'感谢你的支持！'));
        }else{
            return json(array('status'=>0,'info'=>'评论失败！'));
        }
    }

    //评论评价
    public function commentpj(){
        $params = $this->request->post();
        $LpcommentModel = new LpcommentModel;
        if ($params['type']==1) {
            $info = $LpcommentModel->where('id', $params['id'])->setInc('KP_Agree');
        }else{
            $info = $LpcommentModel->where('id', $params['id'])->setInc('KP_Disapprove');
        }
        
        if ($info) {
            return json(array('info'=>'Yes'));
        }else{
            return json(array('info'=>'No'));
        }
    }

    //看房专车
    public function zhuanche($house=''){
        $this->view->assign('house',$house);
        return $this->view->fetch("index/house_zhuanche");
    }

    public function lpvideo($ids=''){
        $common = $this->LpInfo($ids);
        $VideoModel = new VideoModel;
        $index['videoinfo'] = $VideoModel->field('id,KP_Title,KP_PicUrl,KP_VideoUrl,KP_OverallUrl')->where('KP_LpID',$ids)->limit(1)->select();
        
        $LphxpicModel = new LphxpicModel;
        $index['hxview'] = $LphxpicModel->field('id,KP_PicUrl,KP_AllViewUrl,KP_Hx,KP_Area')->where('KP_LpID',$ids)->where('KP_AllViewUrl','<>','')->select();
        $this->view->assign('index',array_merge($index, $common));
        return $this->view->fetch("index/house_video");
    }

}
