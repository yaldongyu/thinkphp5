<?php

namespace app\mobile\controller;
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
use app\admin\model\Video as VideoModel;
use app\admin\model\Zhcity;
use think\Db;
use think\Cookie;
class House extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize(){
        parent::_initialize();
    }

    //新房首页
    public function index($Sokey=''){
        /*if ($Sokey) {
            $wheres = " KP_LpName like '%".$Sokey."%'";
            $wherec =  "1=1";
        }else{
            $wheres = "1=1";
            $wherec =  $this->WhereCity;
        }*/
        $selectkey='';
        if (Cookie::get("Ts")||Cookie::get("Wy")) {
            $wheres = " KP_TsType like '%".urldecode(Cookie::get("Ts"))."%' and KP_Wylx like '%".urldecode(Cookie::get("Wy"))."%' ";
            if (Cookie::get("Ts")) {
                $selectkey = "Ts";
            }else{
                $selectkey = "Wy";
            }
        }else{
            $wheres = "1=1";
        }
        if (Cookie::get("City")) {
           $wherec =  '(KP_Provice = '.Cookie::get("City").' or KP_Citys = '.Cookie::get("City").' or KP_District = '.Cookie::get("City").' or KP_County = '.Cookie::get("City").')';
           $selectkey = "City";
        }else{
           $wherec = $this->WhereCity;
        }

        if (Cookie::get("Price")) {
            $pricearr = explode('-', urldecode(Cookie::get("Price")));
            $whereprice = 'b.KP_Qiprice > '.$pricearr[0].' and b.KP_Qiprice < '.$pricearr[1];
            $selectkey = "Price";
        }else{
            $whereprice = "1=1";
        }
        
        if (Cookie::get("Sokey")) {
            $wheres = " KP_LpName like '%".urldecode(Cookie::get("Sokey"))."%'";
            $wherec =  "1=1";
        }else{
            $wheres = "1=1";
        }
        //echo $wheres;
         
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
                                   ->where("areapid",0)
                                   ->where('status',1)
                                   ->cache()
                                   ->order('weigh Desc')
                                   ->select();
        foreach ($index['sxqy'] as $key => $value) {
           //if ($this->City) {
                $areawhere = '(KP_Provice = '.$value['areaid'].' or KP_Citys = '.$value['areaid'].' or KP_District = '.$value['areaid'].' or KP_County = '.$value['areaid'].')';
                $index['sxqy'][$key]['quyu'] = $AreaModel->field('id,name,areaid')
                                                   ->where($areawhere)
                                                   ->where("areapid",'<>',0)
                                                   ->where('status',1)
                                                   ->cache()
                                                   ->order('weigh Desc')
                                                   ->select();
            /*}else{
                $index['sxqy'][$key]['quyu'] = $AreaModel->field('id,name,areaid')
                                                   ->where('areapid',$value['areaid'])
                                                   ->where("areapid",'<>',0)
                                                   ->where('status',1)
                                                   ->cache()
                                                   ->order('weigh Desc')
                                                   ->select();
            }*/
        }

        $YwtypeModel = new YwtypeModel;      
        $index['sxlx'] = $YwtypeModel->field('id,KP_Name')->where('KP_Type',0)->limit(10)->select();
        $index['sxts'] = $YwtypeModel->field('id,KP_Name')->where('KP_Type',1)->limit(10)->select();
        $index['sxjg'] = $YwtypeModel->field('id,KP_Name,KP_Content')->where('KP_Type',2)->select();
        $LpModel = new LpModel;
        /*$lpmodel = $LpModel
                        ->field('id,KP_Wjt,KP_LpName,Kp_gflm,KP_City,Kp_tel,KP_Fjh,KP_Xszt,KP_Wylx,KP_TsType,KP_Lpdz,KP_YouHui,KP_TaoJia')
                        ->where($this->WhereCity)
                        ->limit(0,20)
                        ->order('KP_Top desc,KP_EditTime desc')
                        ->buildSql();

        $index['list'] = $LpModel->table($lpmodel)->alias('a')
                        ->join('lpprice b','b.KP_LpID=a.id','left')
                        ->field('a.id,a.KP_Wjt,a.KP_LpName,a.Kp_gflm,a.KP_City,a.Kp_tel,a.KP_Fjh,a.KP_Xszt,a.KP_Wylx,a.KP_TsType,a.KP_Lpdz,a.KP_YouHui,a.KP_TaoJia,b.KP_Qiprice,b.KP_Juprice,b.KP_ValidityStart,b.KP_ValidityEnd')
                        ->select();   
        $index['total'] = $LpModel
                        ->field('id')
                        ->where($this->WhereCity)
                        ->count('id');
        foreach ($index['list'] as $key => $value) {
            $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAME');     
            $value['KP_Qiprice'] = floatval($value['KP_Qiprice']);
            $value['KP_Juprice'] = floatval($value['KP_Juprice']);
            $value['KP_ValidityStart'] = date("Y年m月d日",strtotime($value['KP_ValidityStart']));
            $value['KP_ValidityEnd'] = date("Y年m月d日",strtotime($value['KP_ValidityEnd']));
            $value['KP_Wjt'] = str_replace('Max', 'Min', $value['KP_Wjt']);
        }*/

        if ($whereprice=="1=1") {
            $index['list'] = $LpModel->alias('a')
                            ->field('id,KP_Wjt,KP_LpName,Kp_gflm,KP_City,Kp_tel,KP_Fjh,KP_Xszt,KP_Wylx,KP_TsType,KP_Lpdz,KP_YouHui,KP_TaoJia')
                            ->where($wheres)
                            ->where($wherec)
                            ->limit(0,20)
                            ->order('KP_Top desc,KP_EditTime desc')
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
                $value['KP_Wjt'] = str_replace('Max', 'Min', $value['KP_Wjt']);
                $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
            }           
            $index['total'] = $LpModel
                            ->field('id')
                            ->where($wheres)
                            ->where($wherec)
                            ->count('id');
        }else{
            $index['list'] = $LpModel->alias('a')
                        ->join('lpprice b','b.KP_LpID=a.id')
                        ->field('a.id,a.KP_Wjt,a.KP_LpName,a.Kp_gflm,a.KP_City,a.Kp_tel,a.KP_Fjh,a.KP_Xszt,a.KP_Wylx,a.KP_TsType,a.KP_Lpdz,a.KP_YouHui,a.KP_TaoJia,
                                 b.KP_Qiprice,b.KP_Juprice,b.KP_ValidityStart,b.KP_ValidityEnd')
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
            foreach ($index['list'] as $key => $value) {
                $value['KP_Wjt'] = str_replace('Max', 'Min', $value['KP_Wjt']);
                $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
                $value['KP_Qiprice'] = floatval($value['KP_Qiprice']);
                $value['KP_Juprice'] = floatval($value['KP_Juprice']);
            } 
        }
        //print_r(collection($index['sxjg'])->toArray());exit;
        $this->view->assign("index",$index);
        $this->view->assign('areatext',$AreaModel->where('areaid',$this->City)->value('name'));
        $this->view->assign('selectkey',$selectkey);
        return $this->view->fetch("index/house");
    }

    //新房数据接口
    public function indexapi(){
        $params = $this->request->post();
        $page = isset($params['page'])?($params['page']?$params['page']:1):1; 
        $pagesize = isset($params['pagesize'])?$params['pagesize']:10;
        $pages = ($page-1)*$pagesize;
        $area = isset($params['ct'])?($params['ct']?$params['ct']:$this->City):$this->City;
        $price = isset($params['tp'])?$params['tp']:0;
        $wuye = isset($params['hx'])?$params['hx']:'';
        $tese = isset($params['ts'])?$params['ts']:'';
        $lx = isset($params['lx'])?$params['lx']:'';
        $wherestr = '1=1';
        $whereprice = '1=1';
        $order = 'a.KP_Top desc,a.KP_EditTime desc';
        if ($price) {
            $pricearr = explode('-', $price);
            $whereprice = 'b.KP_Qiprice > '.$pricearr[0].' and b.KP_Qiprice < '.$pricearr[1];
        }
        //1是热销楼盘 2，降价楼盘 3，购房联盟楼盘
        if ($lx==1) {
           $wherestr .= ' and KP_Yhz=1';
        }else if($lx==2){
            $wherestr .= ' and KP_Jjlp=1';
        }else{
            $wherestr .= ' and KP_Gflm=1';
        }

        //['本月开盘'=>'ThisMonth','下月开盘'=>'NextMonth','三月内开盘'=>'ThreeMonth','半年内开盘'=>'SixMonth','一年内开盘'=>'AYear']
        $LpModel = new LpModel;
        $index['list'] = array();
        if ($price) {
            $index['list'] = $LpModel->alias('a')
                        ->join('lpprice b','b.KP_LpID=a.id')
                        ->field('a.id,a.KP_Wjt,a.KP_LpName,a.Kp_gflm,a.KP_City,a.Kp_tel,a.KP_Fjh,a.KP_Xszt,a.KP_Wylx,a.KP_TsType,a.KP_Lpdz,a.KP_YouHui, a.KP_TaoJia,b.KP_Qiprice,b.KP_Juprice,b.KP_ValidityStart,b.KP_ValidityEnd')
                        ->where('KP_Provice|KP_Citys|KP_District|KP_County',$area)
                        ->where('KP_Wylx','like','%'.$wuye.'%')
                        ->where('KP_TsType','like','%'.$tese.'%')
                        ->where($wherestr)
                        ->where($whereprice)
                        ->order($order)
                        ->limit($pages,$pagesize)->select();
            $index['total'] = $LpModel->alias('a')
                        ->join('lpprice b','b.KP_LpID=a.id')
                        ->field('a.id')
                        ->where('a.KP_Provice|a.KP_Citys|a.KP_District|a.KP_County',$area)
                        ->where('a.KP_Wylx','like','%'.$wuye.'%')
                        ->where('a.KP_TsType','like','%'.$tese.'%')
                        ->where($whereprice)
                        ->where($wherestr)
                        ->count('a.id');       
            foreach ($index['list'] as $key => $value) {
                $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAME');     
                $value['KP_Qiprice'] = floatval($value['KP_Qiprice']);
                $value['KP_Juprice'] = floatval($value['KP_Juprice']);
                $value['KP_ValidityStart'] = date("Y年m月d日",strtotime($value['KP_ValidityStart']));
                $value['KP_ValidityEnd'] = date("Y年m月d日",strtotime($value['KP_ValidityEnd']));
                $value['KP_Wjt'] = str_replace('Max', 'Min', $value['KP_Wjt']);
            }     
        }else{
            /*$lpmodel = $LpModel->alias('a')
                        ->field('id,KP_Wjt,KP_LpName,Kp_gflm,KP_City,Kp_tel,KP_Fjh,KP_Xszt,KP_Wylx,KP_TsType,KP_Lpdz,KP_YouHui,KP_TaoJia,a.KP_Jz')
                        ->where('KP_Provice|KP_Citys|KP_District|KP_County',$area)
                        ->where('KP_Wylx','like','%'.$wuye.'%')
                        ->where('KP_TsType','like','%'.$tese.'%')
                        ->where($wherestr)
                        ->limit($pages,$pagesize)
                        ->order($order)
                        ->buildSql();

            $index['list'] = $LpModel->table($lpmodel)->alias('a')
                        ->join('lpprice b','b.KP_LpID=a.id','left')
                        ->field('a.id,a.KP_Wjt,a.KP_LpName,a.Kp_gflm,a.KP_City,a.Kp_tel,a.KP_Fjh,a.KP_Xszt,a.KP_Wylx,a.KP_TsType,a.KP_Lpdz,a.KP_YouHui,a.KP_TaoJia,b.KP_Qiprice,b.KP_Juprice,b.KP_ValidityStart,b.KP_ValidityEnd')
                        ->select(); */ 


             $index['list'] = $LpModel->alias('a')
                        ->field('id,KP_Wjt,KP_LpName,Kp_gflm,KP_City,Kp_tel,KP_Fjh,KP_Xszt,KP_Wylx,KP_TsType,KP_Lpdz,KP_YouHui,KP_TaoJia,a.KP_Jz')
                        ->where('KP_Provice|KP_Citys|KP_District|KP_County',$area)
                        ->where('KP_Wylx','like','%'.$wuye.'%')
                        ->where('KP_TsType','like','%'.$tese.'%')
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
                        ->order('id asc')
                        ->select();
            $pricearr = array();            
            foreach ($prices as $key => $value) {
                $pricearr[$value['KP_LpID']] = $value;
            }
            foreach ($index['list'] as $key => $value) {
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
                $value['KP_Wjt'] = str_replace('Max', 'Min', $value['KP_Wjt']);
                $value['CNAME'] = db('zhcity')->where('id',$value['KP_City'])->cache()->value('CNAMES');
            }            

                         
            $index['total'] = $LpModel
                        ->field('id')
                        ->where('KP_Provice|KP_Citys|KP_District|KP_County',$area)
                        ->where('KP_Wylx','like','%'.$wuye.'%')
                        ->where('KP_TsType','like','%'.$tese.'%')
                        ->where($wherestr)
                        ->count('id');       
        }
                                     
        $data = collection($index)->toArray();
        return json($data);
    }

    //新房详细页
    public function house_details($ids = NULL){   
        //楼盘信息 
        $common = $this->LpInfo($ids);
        //楼盘动态
        $LpModel = new LpModel;
        $NewsModel = new NewsModel;
        $index['lpnews'] = $NewsModel->field('id,KP_Title,KP_AddTime,KP_TagKey,KP_Ly')->where(['KP_LpID'=>$ids])->order('KP_Zd desc,id desc')->limit(3)->select();
        $VideoModel = new VideoModel;
        $videoinfo = $VideoModel->field('KP_VideoUrl,KP_PicUrl')->where('KP_LpID',$ids)->find();
        if ($videoinfo) {
            $index['KP_VideoUrl'] = $videoinfo['KP_VideoUrl'];     
            $index['KP_VideoLogo'] = $videoinfo['KP_PicUrl']; 
        }else{
            $index['KP_VideoUrl'] = '';     
            $index['KP_VideoLogo'] = ''; 
        }
        //项目价格走势
        $LppriceModel = new LppriceModel;
        $index['lpjg'] = $LppriceModel->field('KP_Qiprice,KP_Juprice,KP_ValidityStart,KP_ValidityEnd')->where('KP_LpID',$ids)->order('KP_JgTime desc')->find();
        if($index['lpjg']){
            $index['lpjg']['KP_Qiprice'] = floatval($index['lpjg']['KP_Qiprice']);
            $index['lpjg']['KP_Juprice'] = floatval($index['lpjg']['KP_Juprice']);
            $index['lpjg']['KP_ValidityStart'] = date("Y年m月d日",strtotime($index['lpjg']['KP_ValidityStart']));
            $index['lpjg']['KP_ValidityEnd'] = date("Y年m月d日",strtotime($index['lpjg']['KP_ValidityEnd']));
        }else{
            $index['lpjg']['KP_Qiprice'] = 0;
            $index['lpjg']['KP_Juprice'] = 0;
            $index['lpjg']['KP_ValidityEnd'] = '';
            $index['lpjg']['KP_ValidityStart'] = '';
        }

        //相册数量
        $LpalbumModel = new LpalbumModel;
        $LppicModel = new LppicModel;
        $index['lpalbum'] = $LpalbumModel->where("KP_LpID",$ids)->buildSql();
        $index['xcnum'] = $LpalbumModel->table($index['lpalbum'])->alias('a')
                                         ->join('lppic b','a.id=b.KP_HcID')
                                         ->field('a.id')
                                         ->count("a.id");
        //项目介绍 周边配套
        $LpcontentModel = new LpcontentModel;
        $index['xmjs'] = $LpcontentModel->field('KP_Lpnr')->where('KP_Lm',0)->where('KP_LpID',$ids)->order('id desc')->find();
        $index['zbpt'] = $LpcontentModel->field('KP_Lpnr')->where('KP_Lm',1)->where('KP_LpID',$ids)->order('id desc')->find();                                 
        //更新浏览次数
        $LpModel->where('id', $ids)->setInc('KP_Cs');                         
        $this->view->assign('index',array_merge($index, $common));
        return $this->view->fetch("index/house_details");
    }

    //楼盘户型图
    public function lphx($ids='',$typeid=''){
        //楼盘信息 
        $LpModel = new LpModel;
        $LppriceModel = new LppriceModel;
        $index['LpInfo'] = $LpModel->field('id,KP_LpName,KP_Xszt,KP_Zxqk,KP_Wylx,KP_Lpdz,KP_Logo,KP_Tel,KP_TaoJia')->where('id',$ids)->find();
        $index['LpInfo']['KP_Juprice'] = floatval($LppriceModel->where('KP_LpID',$ids)->order('id desc')->value('KP_Juprice')); 
        $zx = LpModel::getTypeList();
        $index['LpInfo']['KP_Zxqk'] = $zx[$index['LpInfo']['KP_Zxqk']];
        //户型
        $LphxpicModel = new LphxpicModel;
        $hxarr = LphxpicModel::getHxTypeList();
        $index['Types'] = $LphxpicModel->field('count(KP_Type) as num,KP_Type')->where('KP_LpID',$ids)->group('KP_Type')->select();
        $index['rows'] = array();
        foreach ($index['Types'] as $key => $value) {
            $value['name'] = $hxarr[$value['KP_Type']];
            if ($key==0&&$typeid=='') {
                $typeid = $value['KP_Type'];                     
            }
        }
     
            $index['rows'] = $LphxpicModel->field('id, KP_LpID, KP_Type, KP_Hxbh, KP_Hx, KP_Area, KP_PicUrl,KP_AddTime')
                                      ->where('KP_LpID',$ids)->where('KP_Type',$typeid)->select(); 
            if ($index['rows']) {
                $index['Type_name'] = $hxarr[$index['rows'][0]['KP_Type']];
            }else{
                $index['Type_name'] = '';
            }                         
        foreach ($index['rows'] as $key => $value) {
            $value['KP_PicUrl'] = str_replace('Max', 'Min', $value['KP_PicUrl']);
        }
        //print_r($index['rows']);exit;     
        $this->view->assign('index',$index);
        return $this->view->fetch("index/house_huxing");
    }

    //楼盘相册index
    public function lpxc($ids='',$typeid=''){
        //楼盘信息 
        $LpModel = new LpModel;
        $LppriceModel = new LppriceModel;
        $index['LpInfo'] = $LpModel->field('id,KP_LpName,KP_Zxqk,KP_Xszt,KP_Wylx,KP_Lpdz,KP_Logo,KP_Tel,KP_TaoJia')->where('id',$ids)->find();
        $index['LpInfo']['KP_Juprice'] = floatval($LppriceModel->where('KP_LpID',$ids)->order('id desc')->value('KP_Juprice')); 
        $zx = LpModel::getTypeList();
        $index['LpInfo']['KP_Zxqk'] = $zx[$index['LpInfo']['KP_Zxqk']];

        //相册
        $LpalbumModel = new LpalbumModel;

        $index['Types'] = $LpalbumModel->alias("a")
                                       ->join('lppic b','a.id=b.KP_HcID')
                                       ->field('count(a.KP_HcName) as num,a.KP_HcName,b.KP_PicTitle')
                                       ->where(['a.KP_LpID'=>$ids])
                                       ->group('a.KP_HcName')
                                       ->select();
        $index['rows'] = array();
        foreach ($index['Types'] as $key => $value) {
            if ($key==0&&$typeid=='') {
                $typeid =  $value['KP_HcName'];
            }
        }      
 
        $index['rows'] =  $LpalbumModel->alias("a")
                                       ->join('lppic b','a.id=b.KP_HcID')
                                       ->field('a.id,a.KP_LpID,a.KP_HcName,b.id as bid,b.KP_PicTitle,b.KP_PicUrl,b.KP_AddTime')
                                       ->where(['a.KP_LpID'=>$ids,'a.KP_HcName'=>$typeid])
                                       ->select();
                             
        //print_r(collection($index['rows'])->toArray());exit;               
        foreach ($index['rows'] as $key => $value) {
            $value['KP_PicUrl'] = str_replace('Max', 'Min', $value['KP_PicUrl']);
        }                 
        $this->view->assign('index',$index);
        return $this->view->fetch("index/house_xiangce");
    }
   
    //获取楼盘信息
    public function LpInfo($ids){
        $LpModel = new LpModel;
        $lp = $LpModel->get($ids);
        $lp['KP_KpTime'] = date("Y年m月d日",strtotime($lp['KP_KpTime']));
        $lp['KP_RzTime'] = date("Y年m月d日",strtotime($lp['KP_RzTime']));
        $lp['CNAME'] = Zhcity::where('id',$lp['KP_City'])->value('CNAME');
        $lp['QNAME'] = Zhcity::where('id',$lp['KP_Provice'])->value('CNAME').' '.Zhcity::where('id',$lp['KP_Citys'])->value('CNAME')
                       .' '.Zhcity::where('id',$lp['KP_District'])->value('CNAME').' '.Zhcity::where('id',$lp['KP_County'])->value('CNAME');
        $LpactivityModel = new LpactivityModel;
        $lpyh = $LpactivityModel->where("KP_LpID",$ids)->value("KP_Info");
        if ($lpyh) {
            $lp['KP_YouHui'] = $lpyh;
        }
        $zx = LpModel::getTypeList();
        $lp['KP_Zxqk'] = $zx[$lp['KP_Zxqk']];
        $index['LpInfo'] = $lp;

        return $index;
    }

    //楼盘动态
    public function lpdt($ids=''){
        //楼盘信息 
        $LpModel = new LpModel;
        $LppriceModel = new LppriceModel;
        $index['LpInfo'] = $LpModel->field('id,KP_LpName,KP_Xszt,KP_Zxqk,KP_Wylx,KP_Lpdz,KP_Logo,KP_TaoJia')->where('id',$ids)->find();
        $index['LpInfo']['KP_Juprice'] = floatval($LppriceModel->where('KP_LpID',$ids)->order('id desc')->value('KP_Juprice')); 
        $zx = LpModel::getTypeList();
        $index['LpInfo']['KP_Zxqk'] = $zx[$index['LpInfo']['KP_Zxqk']];
        $NewsModel = new NewsModel;
        $index['lpdt'] = $NewsModel->field('id,KP_PicUrl,KP_Cs,KP_Title,KP_Description,KP_AddTime')->where('KP_LpID',$ids)->order('KP_Zd desc,id desc')->select();
        
        $this->view->assign('index',$index);
        return $this->view->fetch("index/dongtai");
    }

    //楼盘点评 KP_LpID KP_Score KP_Phone KP_Content KP_Time KP_Agree KP_Disapprove KP_Check KP_KhName
    public function comment(){
        $LpcommentModel = new LpcommentModel;
        $params = $this->request->post();
        if ($params) {
            try {
                $params['KP_Time'] = date("Y-m-d H:i:s");
                $result =  $LpcommentModel->allowField(true)->save($params);
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error($LpcommentModel->getError());
                }
            } catch (\think\exception\PDOException $e) {
                $this->error($e->getMessage());
            } catch (\think\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }

}
