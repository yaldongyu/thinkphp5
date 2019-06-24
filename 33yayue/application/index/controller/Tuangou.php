<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\common\library\Token;
use app\admin\model\Lp as LpModel;
use app\admin\model\Lptg as LptgModel;
use app\admin\model\Kftbm as KftbmModel;
use app\admin\model\Area as AreaModel;
use app\admin\model\Ywtype as YwtypeModel;
use app\admin\model\Lpprice as LppriceModel;
use think\Db;
class Tuangou extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = 'default';

    public function _initialize(){
        parent::_initialize();
    }

    public function index(){
        $LptgModel = new LptgModel;
        $index['list'] = $LptgModel->field('id,KP_Title,KP_Htzt,KP_PicUrl,KP_EndTime,KP_Yhby,KP_Yhby2,KP_LpID')
                                ->where($this->WhereCity)
                                ->where('KP_Htzt',0)
                                ->limit(8)
                                ->order("weigh desc")
                                ->select();
        $index['total'] = $LptgModel->field('id')
                                ->where($this->WhereCity)
                                ->where('KP_Htzt',0)
                                ->count("id");
        $arr = array();
        $lpid = array();
        foreach ($index['list'] as $key => $value) {
            $arr[] = $value['id'];
            $lpid[] = $value['KP_LpID'];
        }

        $KftbmModel = new KftbmModel;                
        $bm = $KftbmModel->field('count(KP_TgID) as num,KP_TgID')
                            ->where('KP_TgID','in',$arr)
                            ->group('KP_TgID')->select();
        foreach ($bm as $k => $v) {
            $bmnum[$v['KP_TgID']][] = $v;
        }
        //print_r(collection($bm)->toArray());exit;  
        $LpModel = new LpModel;
          $lparr = $LpModel
                      ->field('id,KP_City,KP_Lpdz,KP_Zlhx,KP_TaoJia,KP_Tel')
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
        
       // print_r(collection($bmnum)->toArray());exit;  
        foreach ($index['list'] as $key => $value) {
            $value['bmnum'] = isset($bmnum[$value['id']][0]['num'])?$bmnum[$value['id']][0]['num']:0;    
            $value['KP_EndTime'] = $this->getSends($value['KP_EndTime']);//$this->getRemainderTime(time(),strtotime($value['KP_EndTime']),4);
            $value['KP_Lpdz'] = $lpinfoarr[$value['KP_LpID']]['KP_Lpdz'];
            $value['KP_TaoJia'] = $lpinfoarr[$value['KP_LpID']]['KP_TaoJia'];
            $value['KP_Zlhx'] = $lpinfoarr[$value['KP_LpID']]['KP_Zlhx'];
            $value['KP_Juprice'] = $lpinfoarr[$value['KP_LpID']]['KP_Juprice']?$lpinfoarr[$value['KP_LpID']]['KP_Juprice'].'元/㎡':($lpinfoarr[$value['KP_LpID']]['KP_TaoJia']?$lpinfoarr[$value['KP_LpID']]['KP_TaoJia'].'万元/套':'待定');
            $value['pricetxt'] = $lpinfoarr[$value['KP_LpID']]['KP_Juprice']?'参考均价':($lpinfoarr[$value['KP_LpID']]['KP_TaoJia']?'参考总价':'');
            $value['KP_Tel'] = $lpinfoarr[$value['KP_LpID']]['KP_Tel'];
            $value['CNAME'] = db('zhcity')->where('id',$lpinfoarr[$value['KP_LpID']]['KP_City'])->cache()->value('CNAMES');
        }
        
       //print_r(collection($index['list'])->toArray()); exit;  
       $this->view->assign("index",$index);                      
        return $this->view->fetch("index/tuangou");
    }

    public function detail($ids=''){
            $index['content'] = LptgModel::get($ids);
            $index['content']['KP_EditTime'] = $this->getSends($index['content']['KP_EndTime']);
            //楼盘信息
            $LpModel = new LpModel;
            $LppriceModel = new LppriceModel;
            $index['Lpinfo'] = $LpModel->field('id,KP_LpName,KP_Zlhx,KP_Tel,KP_YouHui,KP_Wjt,KP_City,KP_Citys,KP_TaoJia,KP_TsType,KP_Lpdz')
                                        ->where('id',$index['content']['KP_LpID'])
                                        ->find();
            $priceobj = $LppriceModel->field('KP_Qiprice,KP_Juprice')->where('KP_LpID',$index['content']['KP_LpID'])->find();
            if ($priceobj) {
                $index['Lpinfo']['KP_Qiprice'] = floatval($priceobj['KP_Qiprice']);
                $index['Lpinfo']['KP_Juprice'] = floatval($priceobj['KP_Juprice']);
            }else{
                $index['Lpinfo']['KP_Qiprice'] = 0;
                $index['Lpinfo']['KP_Juprice'] = 0;
            }
            $index['Lpinfo']['pricetxt'] = $index['Lpinfo']['KP_Juprice']?'参考均价':($index['Lpinfo']['KP_TaoJia']?'参考总价':'');
            $index['Lpinfo']['KP_Juprice'] = $index['Lpinfo']['KP_Juprice']?$index['Lpinfo']['KP_Juprice'].'元/㎡':($index['Lpinfo']['KP_TaoJia']?$index['Lpinfo']['KP_TaoJia'].'万元/套':'待定');

            $index['Lpinfo']['CNAME'] = db('zhcity')->where('id',$index['Lpinfo']['KP_City'])->cache()->value('CNAME');
            //报名人数
            $KftbmModel = new KftbmModel;
            $index['Lpinfo']['bmnum'] = $KftbmModel->field('id')->where("KP_TgID",$ids)->count("id");
            //热门推荐
            $LpModel = new LpModel;
            $index['rmlp'] = $LpModel->alias("a")
                                 ->field('a.id,a.KP_LpName,a.KP_TaoJia,KP_Wjt')
                                 ->where($this->WhereCityA)
                                 ->where("a.KP_Yhz",1)
                                 ->order('a.KP_Top desc ,a.KP_EditTime desc')
                                 ->limit(9)
                             ->select();

            $arr = array();
            foreach ($index['rmlp'] as $key => $value) {
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
            foreach ($index['rmlp'] as $key => $value) {
                if (isset($pricearr[$value['id']])) {
                    $value['KP_Qiprice'] = floatval($pricearr[$value['id']]['KP_Qiprice']);
                    $value['KP_Juprice'] = floatval($pricearr[$value['id']]['KP_Juprice']);
                }else{
                    $value['KP_Qiprice'] = 0;
                    $value['KP_Juprice'] = 0;
                }
                $value['KP_Juprice'] = $value['KP_Juprice']?$value['KP_Juprice'].'元/㎡':($value['KP_TaoJia']?$value['KP_TaoJia'].'万元/套':'待定');
            }
            
           // print_r(collection($index['Lpinfo'])->toArray()); exit;     
            $this->view->assign("index",$index);     
            return $this->view->fetch("index/tuangou_detail");
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
           $where .= ' and (b.KP_Provice = '.$city.' or b.KP_Citys='.$city.' or b.KP_District='.$city.' or b.KP_County='.$city.')';
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

    function getSends($endtime){
        $time1 = strtotime($endtime);
        $time2 = strtotime('now');;
        //相减得到相差的 秒 数
        $time3 = $time1 - $time2;
        return $time3;

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
