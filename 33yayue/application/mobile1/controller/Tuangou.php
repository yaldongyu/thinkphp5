<?php

namespace app\mobile1\controller;

use app\common\controller\Frontend;
use app\common\library\Token;
use app\admin\model\Lp as LpModel;
use app\admin\model\Lptg as LptgModel;
use app\admin\model\Kftbm as KftbmModel;
use app\admin\model\Area as AreaModel;
use app\admin\model\Ywtype as YwtypeModel;
use app\admin\model\Lpprice as LppriceModel;
use app\admin\model\Zhcity as ZhcityModel;
use think\Db;
class Tuangou extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize(){
        parent::_initialize();
    }

    public function index($typeid=0){
        $LptgModel = new LptgModel;
        $AreaModel = new AreaModel;
        $ZhcityModel = new ZhcityModel;
        $index['list'] = $LptgModel->field('id,KP_Title,KP_PicUrl,KP_Provice,KP_Citys,KP_District,KP_County,KP_Yhxq,KP_EndTime,KP_Yhby,KP_Yhby2')
                                ->where('KP_Htzt',$typeid)
                                ->where($this->WhereCity)
                                ->order("weigh desc")
                                ->select();
        if (count($index['list'])==0) {
            if (!$this->City) {
              $where = 'KP_Provice='.$this->City;
            }else{
              $hdpareaid = $AreaModel->where('areaid',$this->City)->value('KP_Provice'); 
              $where = 'KP_Provice='.$hdpareaid;
            }
            
            $index['list'] = $LptgModel->field('id,KP_Title,KP_PicUrl,KP_Provice,KP_Citys,KP_District,KP_County,KP_Yhxq,KP_EndTime,KP_Yhby,KP_Yhby2')
                                ->where('KP_Htzt',$typeid)
                                ->where($where)
                                ->order("weigh desc")
                                ->select();
        }
        $arr = array();
        foreach ($index['list'] as $key => $value) {
            $arr[] = $value['id'];
        }

        $KftbmModel = new KftbmModel;                
        $bm = $KftbmModel->field('count(KP_HouseID) as num,KP_HouseID')
                            ->where('KP_HouseID','in',$arr)
                            ->group('KP_HouseID')->select();
        foreach ($bm as $k => $v) {
            $bmnum[$v['KP_HouseID']][] = $v;
        }                    
        foreach ($index['list'] as $key => $value) {
            $value['bmnum'] = isset($bmnum[$value['id']][0]['num'])?$bmnum[$value['id']][0]['num']:0;    
            $value['KP_EndTime'] = $this->getRemainderTime(time(),strtotime($value['KP_EndTime']),4);
            $prov = $ZhcityModel->where('id',$value['KP_Provice'])->cache()->value('CNAMES');
            if ($prov) {
                $ci = $value['KP_County']?$value['KP_County']:($value['KP_District']?$value['KP_District']:$value['KP_Citys']);
                $city = $ZhcityModel->where('id',$ci)->cache()->value('CNAMES');
                $value['CNAME'] = $prov.'-'.$city;
            }else{
                $value['CNAME'] = '-';
            }
        }
        
        //print_r(collection($index['list'])->toArray()); exit;  
        $this->view->assign("index",$index);                      
        return $this->view->fetch("index/tuangou");
    }

    public function detail($ids=''){
            $index['content'] = LptgModel::get($ids);
            //楼盘信息
            $LpModel = new LpModel;
            $LppriceModel = new LppriceModel;
            $index['Lpinfo'] = $LpModel->field('id,KP_LpName,KP_YouHui,KP_Tel,KP_Zlhx,KP_City,KP_Citys,KP_Wjt,KP_TaoJia,KP_TsType,KP_Lpdz')->where('id',$index['content']['KP_LpID'])->find();
               
            $priceobj = $LppriceModel->field('KP_Qiprice,KP_Juprice')->where('KP_LpID',$index['content']['KP_LpID'])->find();
            $index['Lpinfo']['KP_Qiprice'] = floatval($priceobj['KP_Qiprice']);
            $index['Lpinfo']['KP_Juprice'] = floatval($priceobj['KP_Juprice']);
            $index['Lpinfo']['CNAME'] = db('zhcity')->where('id',$index['Lpinfo']['KP_City'])->cache()->value('CNAME');
            $index['content']['KP_Price'] = $index['Lpinfo']['KP_Juprice']?$index['Lpinfo']['KP_Juprice']:($index['Lpinfo']['KP_TaoJia']?$index['Lpinfo']['KP_TaoJia']:'待定');
            $index['content']['KP_Price_unit'] = $index['Lpinfo']['KP_Juprice']?'元/㎡':($index['Lpinfo']['KP_TaoJia']?'万元/套':'');
            $index['content']['KP_Price_title'] = $index['Lpinfo']['KP_Juprice']?'均价：':($index['Lpinfo']['KP_TaoJia']?'套价：':'');
            //报名人数
            $KftbmModel = new KftbmModel;
            $index['Lpinfo']['bmnum'] = $KftbmModel->field('id')->where("KP_HouseID",$ids)->count("id");
            
           // print_r(collection($index['Lpinfo'])->toArray()); exit;  
            $this->view->assign("index",$index);     
            return $this->view->fetch("index/tuangou-details");
    }

    function substr_cut($user_name){
        $strlen     = mb_strlen($user_name, 'utf-8');
        $firstStr     = mb_substr($user_name, 0, 1, 'utf-8');
        $lastStr     = mb_substr($user_name, -1, 1, 'utf-8');
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("**", $strlen - 2);
    }
   
    public function classlptg(){
        $params = $this->request->post(); 
        $LpModel = new LpModel;
        $LptgModel = new LptgModel;
        $page = isset($params['page'])?($params['page']?$params['page']:1):1; 
        $pagesize = isset($params['pagesize'])?$params['pagesize']:10;
        $pages = ($page-1)*$pagesize;
        $wy = isset($params['Wy'])?$params['Wy']:'';
        $city = isset($params['City'])?$params['City']:$this->City;
        $where = '1=1';
        if ($city) {
           $where .= ' and (a.KP_Provice = '.$city.' or a.KP_Citys='.$city.' or a.KP_District='.$city.' or a.KP_County='.$city.')';
        }
        $index['list'] = $LpModel->alias('a')
                           ->join('lptg b','a.id=b.KP_LpID')
                           ->field('b.id,b.KP_Title,b.KP_PicUrl,b.KP_Yhxq,b.KP_EndTime,b.KP_Yhby,b.KP_Yhby2')
                           ->where('a.KP_Wylx','like','%'.$wy.'%')
                           ->where($where)
                           ->where('KP_Htzt',0)
                           ->order('b.weigh Desc')->limit($pages,$pagesize)
                           ->select();
        $index['total'] = $LpModel->alias('a')
                           ->join('lptg b','a.id=b.KP_LpID')
                           ->field('b.id,b.KP_Title,b.KP_PicUrl,b.KP_Yhxq,b.KP_EndTime,b.KP_Yhby,b.KP_Yhby2')
                           ->where('a.KP_Wylx','like','%'.$wy.'%')
                           ->where($where)
                           ->where('KP_Htzt',0)
                           ->count();

        $arr = array();
        foreach ($index['list'] as $key => $value) {
            $arr[] = $value['id'];
        }                   
        $KftbmModel = new KftbmModel;                
        $bm = $KftbmModel->field('count(KP_HouseID) as num,KP_HouseID')
                            ->where('KP_HouseID','in',$arr)
                            ->group('KP_HouseID')->select();
        foreach ($bm as $k => $v) {
            $bmnum[$v['KP_HouseID']][] = $v;
        }                    
        foreach ($index['list'] as $key => $value) {
            $value['bmnum'] = isset($bmnum[$value['id']][0]['num'])?$bmnum[$value['id']][0]['num']:0;    
            $value['KP_EndTime'] = $this->getRemainderTime(time(),strtotime($value['KP_EndTime']),4);
        }                   

        $data = collection($index)->toArray();
        return json($data);
    }

    /**
     * 返回两个时间的相距时间，*年*月*日*时*分*秒
     * @param int $one_time 时间一
     * @param int $two_time 时间二
     * @param int $return_type 默认值为0，0/不为0则拼接返回，1/*秒，2/*分*秒，3/*时*分*秒/，4/*日*时*分*秒，5/*月*日*时*分*秒，6/*年*月*日*时*分*秒
     * @param array $format_array 格式化字符，例，array('年', '月', '日', '时', '分', '秒')
     * @return String or false
     */
    function getRemainderTime($one_time, $two_time, $return_type=0, $format_array=array('年', '月', '</font>天<font>', '</font>时<font>', '</font>分<font>', '</font>秒')){
        if ($return_type < 0 || $return_type > 6) {
            return false;
        }
        if (!(is_int($one_time) && is_int($two_time))) {
            return false;
        }
        $remainder_seconds = abs($one_time - $two_time);
        //年
        $years = 0;
        if (($return_type == 0 || $return_type == 6) && $remainder_seconds - 31536000 > 0) {
            $years = floor($remainder_seconds / (31536000));
        }
        //月
        $monthes = 0;
        if (($return_type == 0 || $return_type >= 5) && $remainder_seconds - $years * 31536000 - 2592000 > 0) {
            $monthes = floor(($remainder_seconds - $years * 31536000) / (2592000));
        }
        //日
        $days = 0;
        if (($return_type == 0 || $return_type >= 4) && $remainder_seconds - $years * 31536000 - $monthes * 2592000 - 86400 > 0) {
            $days = floor(($remainder_seconds - $years * 31536000 - $monthes * 2592000) / (86400));
        }
        //时
        $hours = 0;
        if (($return_type == 0 || $return_type >= 3) && $remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - 3600 > 0) {
            $hours = floor(($remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400) / 3600);
        }
        //分
        $minutes = 0;
        if (($return_type == 0 || $return_type >= 2) && $remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - $hours * 3600 - 60 > 0) {
            $minutes = floor(($remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - $hours * 3600) / 60);
        }
        //秒
        $seconds = $remainder_seconds - $years * 31536000 - $monthes * 2592000 - $days * 86400 - $hours * 3600 - $minutes * 60;
        $return = false;
        switch ($return_type) {
            case 0:
                if ($years > 0) {
                    $return = $years . $format_array[0] . $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                } else if ($monthes > 0) {
                    $return = $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                } else if ($days > 0) {
                    $return = $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                } else if ($hours > 0) {
                    $return = $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                } else if ($minutes > 0) {
                    $return = $minutes . $format_array[4] . $seconds . $format_array[5];
                } else {
                    $return = $seconds . $format_array[5];
                }
                break;
            case 1:
                $return = $seconds . $format_array[5];
                break;
            case 2:
                $return = $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            case 3:
                $return = $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            case 4:
                $return = $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            case 5:
                $return = $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            case 6:
                $return = $years . $format_array[0] . $monthes . $format_array[1] . $days . $format_array[2] . $hours . $format_array[3] . $minutes . $format_array[4] . $seconds . $format_array[5];
                break;
            default:
                $return = false;
        }
        return $return;
    }

}
