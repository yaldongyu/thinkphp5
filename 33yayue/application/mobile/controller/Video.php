<?php

namespace app\mobile\controller;

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
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index($typeid="")
    {
        $VideoModel = new VideoModel;
        //三大类 
        $index['type'] = VideoModel::getTypeList();
        if ($typeid!='') {
            $index['list'] = $VideoModel->field('id,KP_Title,KP_Type,KP_PicUrl,KP_Cs')
                                    //->where($this->WhereCity)
                                    ->where('KP_Type',$typeid)
                                    ->order('id desc')
                                    ->limit(20)->select();
            $index['total'] = $VideoModel->field('id,KP_Title,KP_Type,KP_PicUrl,KP_Cs')
                                    //->where($this->WhereCity)
                                    ->where('KP_Type',$typeid)
                                    ->order('id desc')
                                    ->count();
        }else{
            $index['list'] = $VideoModel->field('id,KP_Title,KP_Type,KP_PicUrl,KP_Cs')
                                    //->where($this->WhereCity)
                                    ->order('id desc')
                                    ->limit(20)->select();
            $index['total'] = $VideoModel->field('id')
                                    //->where($this->WhereCity)
                                    ->order('id desc')
                                    ->count('id');
        }
        
        $this->view->assign("index",$index);
        //print_r(collection($index['row'])->toArray()); exit;                        
        return $this->view->fetch("index/video");
    }

    public function indexapi()
    {
        $params = $this->request->post();
        $page = isset($params['page'])?($params['page']?$params['page']:1):1; 
        $pagesize = isset($params['pagesize'])?$params['pagesize']:20;
        $pages = ($page-1)*$pagesize;
        $typeid = isset($params['typeid'])?$params['typeid']:'';
        $VideoModel = new VideoModel;
        //三大类 
        $index['type'] = VideoModel::getTypeList();
        if ($typeid!='') {
            $index['list'] = $VideoModel->field('id,KP_Title,KP_Type,KP_PicUrl,KP_Cs')
                                    //->where($this->WhereCity)
                                    ->where('KP_Type',$typeid)
                                    ->order('id desc')
                                    ->limit($pages,$pagesize)->select();
            $index['total'] = $VideoModel->field('id,KP_Title,KP_Type,KP_PicUrl,KP_Cs')
                                    //->where($this->WhereCity)
                                    ->where('KP_Type',$typeid)
                                    ->order('id desc')
                                    ->count();
        }else{
            $index['list'] = $VideoModel->field('id,KP_Title,KP_Type,KP_PicUrl,KP_Cs')
                                    //->where($this->WhereCity)
                                    ->order('id desc')
                                    ->limit($pages,$pagesize)->select();
            $index['total'] = $VideoModel->field('id')
                                    //->where($this->WhereCity)
                                    ->order('id desc')
                                    ->count('id');
        }
        
        $data = collection($index)->toArray();
        return json($data);
    }


    public function detail($ids=''){
        $VideoModel  = new VideoModel;
            //视频内容
            $index['content'] = VideoModel::get($ids);
            //如果有楼盘id就显示楼盘信息
            if ($index['content']['KP_LpID']) {
                //楼盘信息
                $LpModel = new LpModel;
                $LppriceModel = new LppriceModel;
                $index['LpInfo'] = $LpModel->field('id,KP_LpName,KP_Zxqk,KP_Xszt,KP_Tel,KP_Wylx,KP_Lpdz,KP_Logo')->where('id',$index['content']['KP_LpID'])->find();
                $index['LpInfo']['KP_Qiprice'] = $LppriceModel->where('KP_LpID',$index['content']['KP_LpID'])->order('id desc')->value('KP_Qiprice'); 
                $zx = LpModel::getTypeList();
                $index['LpInfo']['KP_Zxqk'] = $zx[$index['LpInfo']['KP_Zxqk']];
                //更新播放量
                $VideoModel->where('id', $ids)->setInc('KP_Cs');
                //print_r(collection($index['Lpinfo'])->toArray()); exit;      
                $this->view->assign("index",$index);
                     
                return $this->view->fetch("index/video_details");
            }else{
                //更新播放量
                $VideoModel->where('id', $ids)->setInc('KP_Cs');
                //print_r(collection($index['Lpinfo'])->toArray()); exit;      
                $this->view->assign("index",$index);
                return $this->view->fetch("index/video_details_nolp");
            }

    }
   
   public function classvideo(){

        $params = $this->request->post(); 
        $page = isset($params['page'])?($params['page']?$params['page']:1):1; 
        $pagesize = isset($params['pagesize'])?$params['pagesize']:10;
        $pages = ($page-1)*$pagesize;
        $type = isset($params['type'])?$params['type']:-1;

        $VideoModel = new VideoModel;
        $index['list'] = $VideoModel->field('KP_Type,id,KP_Title,KP_Cs,KP_PicUrl,KP_VideoUrl')
               ->where('KP_Type',$type)
               //->where($this->WhereCity)
               ->order('KP_Cs Desc,id Desc')->limit($pages,$pagesize)
               ->select();
        $index['total'] = $VideoModel->field('id')
               ->where('KP_Type',$type)
               //->where($this->WhereCity)
               ->count("id");
        $data = collection($index)->toArray();
        return json($data);
   }



}
