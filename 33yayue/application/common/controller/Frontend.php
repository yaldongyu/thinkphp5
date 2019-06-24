<?php

namespace app\common\controller;

use app\common\library\Auth;
use think\Config;
use think\Controller;
use think\Hook;
use think\Lang;
use fast\Character;
use think\Db;

use app\admin\model\Rmso as RmsoModel;
use app\admin\model\Area as AreaModel;

/**
 * 前台控制器基类
 */
class Frontend extends Controller
{

    /**
     * 布局模板
     * @var string
     */
    protected $layout = '';

    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [];

    /**
     * 权限Auth
     * @var Auth
     */
    protected $auth = null;
    protected $City = '';
    protected $CityName = '全国';
    protected $WhereCity = '1=1';
    protected $WhereCityA = '1=1';
    protected $WhereCityB = '1=1';
    protected $acname = '';
    public function _initialize()
    {
        /*if(isPhone()){
            $domain = $_SERVER['HTTP_HOST'];
            $this->City = db('area')->where('domain',substr($domain,1,strlen($domain)))->value('areaid');
        }else{
            $this->City = db('area')->where('domain',$_SERVER['HTTP_HOST'])->value('areaid');
        }*/
        $domain = $_SERVER['HTTP_HOST'];
        $carr = db('area')->field('areaid,name')->where('domain',substr($domain,1,strlen($domain)))->cache()->find();
        $City = '';
        if ($carr) {
            $City = $carr['areaid'];
            $this->CityName = $carr['name'];
        }
        $fir = explode('.', $domain);
        if (substr($domain,0,1)=="m"&&$City||$fir[0]=="m") {
            $this->City = $City;
        }else{
            $Cityarr = db('area')->field('areaid,name')->where('domain',$domain)->cache()->find(); 
            if ($Cityarr) {
                $this->City = $Cityarr['areaid'];
                $this->CityName = $Cityarr['name'];
            }
        }
        if (!$this->City) {
           $this->City = ''; 
        }else{
           $this->WhereCity = '(KP_Provice = '.$this->City.' or KP_Citys = '.$this->City.' or KP_District = '.$this->City.' or KP_County = '.$this->City.')';
           $this->WhereCityA = '(a.KP_Provice = '.$this->City.' or a.KP_Citys = '.$this->City.' or a.KP_District = '.$this->City.' or a.KP_County = '.$this->City.')';
           $this->WhereCityB = '(b.KP_Provice = '.$this->City.' or b.KP_Citys = '.$this->City.' or b.KP_District = '.$this->City.' or b.KP_County = '.$this->City.')';
        }
        $AreaModel = new AreaModel;
        $quyu = $AreaModel->field('id,name,areaid,domain,ishot,picurl')
                                   ->where("areapid",0)
                                   ->where("status",1)
                                   ->order('weigh Desc')
                                   ->cache("area_data")
                                   ->select();
        foreach ($quyu as $key => $value) {
           $quyu[$key]['quyu'] = $AreaModel->field('id,name,areaid,domain,ishot,picurl')
                                                   ->where("KP_Provice|KP_Citys|KP_District|KP_County",$value['areaid'])
                                                   ->where("areapid",'<>',0)
                                                   ->where("status",1)
                                                   ->order('weigh Desc')
                                                   ->cache('area_data'.$key)
                                                   ->select();
        }
        $hotquyu = $AreaModel->field('id,name,areaid,domain,ishot,picurl,KP_Provice')
                                    ->where("areapid",'<>',0)
                                   ->where("status",1)
                                   ->where("ishot",1)
                                   ->order('weigh Desc')
                                   ->select();
        foreach ($hotquyu as $key => $value) {
            $value['KP_Provice'] =  db('zhcity')->where('id',$value['KP_Provice'])->cache()->value('CNAMES');                         
        }                           
        if ($this->City) {
            $prov = $AreaModel->where('areaid',$this->City)->value("KP_Provice");
        }else{
            $prov = 0;
        }
        
        //移除HTML标签
        $this->request->filter('strip_tags');
        $modulename = $this->request->module();
        $controllername = strtolower($this->request->controller());
        $actionname = strtolower($this->request->action());
        if ($actionname) {
            $this->acname = $actionname;
        }
        
        // 如果有使用模板布局
        if ($this->layout) {
            $this->view->engine->layout('layout/' . $this->layout);
        }
        $this->auth = Auth::instance();

        // token
        $token = $this->request->server('HTTP_TOKEN', $this->request->request('token', \think\Cookie::get('token')));

        $path = str_replace('.', '/', $controllername) . '/' . $actionname;
        // 设置当前请求的URI
        $this->auth->setRequestUri($path);
        // 检测是否需要验证登录
        if (!$this->auth->match($this->noNeedLogin)) {
            //初始化
            $this->auth->init($token);
            //检测是否登录
            if (!$this->auth->isLogin()) {
                $this->error(__('Please login first'), 'user/login');
            }
            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight)) {
                // 判断控制器和方法判断是否有对应权限
                if (!$this->auth->check($path)) {
                    $this->error(__('You have no permission'));
                }
            }
        } else {
            // 如果有传递token才验证是否登录状态
            if ($token) {
                $this->auth->init($token);
            }
        }

        $this->view->assign('user', $this->auth->getUser());

        // 语言检测
        $lang = strip_tags($this->request->langset());

        $site = Config::get("site");

        $upload = \app\common\model\Config::upload();

        // 上传信息配置后
        Hook::listen("upload_config_init", $upload);

        // 配置信息
        $config = [
            'site'           => array_intersect_key($site, array_flip(['name', 'cdnurl', 'version', 'timezone', 'languages'])),
            'upload'         => $upload,
            'modulename'     => $modulename,
            'controllername' => $controllername,
            'actionname'     => $actionname,
            'jsname'         => 'frontend/' . str_replace('.', '/', $controllername),
            'moduleurl'      => rtrim(url("/{$modulename}", '', false), '/'),
            'language'       => $lang
        ];
        $config = array_merge($config, Config::get("view_replace_str"));

        Config::set('upload', array_merge(Config::get('upload'), $upload));

        // 配置信息后
        Hook::listen("config_init", $config);
        // 加载当前控制器语言包

        $RmsoModel = new RmsoModel();
        $AreaModel = new AreaModel();
        //热门搜索
        $head['rmso'] = $RmsoModel->field("id,KP_Key,KP_Url,KP_MobileUrl")->cache("rmso_data")->order('id desc')->limit(5)->select();
        $link = db('links')->field('id,KP_Title,KP_Url')->order('id desc')->limit(5)->select();
        //区域
        //$area = $AreaModel->where('status',1)->select();
        //$head['area'] = (new Character)->groupByInitials(collection($area)->toArray(), 'name');
        $this->loadlang($controllername);
        $this->assign('quyu', $quyu);
        $this->assign('hotquyu', $hotquyu);
        $this->assign('site', $site);
        $this->assign('config', $config);
        $this->assign('head',$head);
        $this->assign('link',$link);
        $this->assign('domain',$fir[1].'.'.$fir[2]);
        $this->assign('prov',$prov);
        $this->assign('controllername',$controllername);
        $this->assign('actionname',$actionname);
        $this->assign('lpname','');
        $this->assign('CityName',$this->CityName);
       
    }

    /**
     * 加载语言文件
     * @param string $name
     */
    protected function loadlang($name)
    {
        Lang::load(APP_PATH . $this->request->module() . '/lang/' . $this->request->langset() . '/' . str_replace('.', '/', $name) . '.php');
    }

    /**
     * 渲染配置信息
     * @param mixed $name 键名或数组
     * @param mixed $value 值
     */
    protected function assignconfig($name, $value = '')
    {
        $this->view->config = array_merge($this->view->config ? $this->view->config : [], is_array($name) ? $name : [$name => $value]);
    }

}
