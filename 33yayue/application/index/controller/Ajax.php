<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use think\Lang;
use app\common\library\Email;
use app\admin\model\Kftbm as KftbmModel;
use think\Config;
/**
 * Ajax异步请求接口
 * @internal
 */
class Ajax extends Frontend
{

    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];
    protected $layout = '';

    /**
     * 加载语言包
     */
    public function lang()
    {
        header('Content-Type: application/javascript');
        $callback = $this->request->get('callback');
        $controllername = input("controllername");
        $this->loadlang($controllername);
        //强制输出JSON Object
        $result = jsonp(Lang::get(), 200, [], ['json_encode_param' => JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE]);
        return $result;
    }
    
    /**
     * 上传文件
     */
    public function upload()
    {
        return action('api/common/upload');
    }


    //报名 KP_Name KP_Phone KP_Message KP_HouseID KP_Url KP_Title
   public function LpBaoming(){
        $KftbmModel = new KftbmModel;
        $params = $this->request->post();
        if ($params) {
            try {
                //$request = Request::instance();
                $data['KP_Type'] = $params['sType'];
                $data['KP_Name'] = $params['T_KftName'];
                $data['KP_Phone'] = $params['T_KftPhone'];
                $data['KP_Title'] = $params['T_Title'];
                $data['KP_Message'] = $params['T_Message'];
                $data['KP_HouseID'] = isset($params['T_LpID'])?$params['T_LpID']:0;
                $data['KP_TgID'] = isset($params['T_TgID'])?$params['T_TgID']:0;
                $data['KP_Url'] = $params['T_PageUrl'];//$request->url();
                $data['KP_BmTime'] = date("Y-m-d H:i:s");
                if ($data['KP_Type']=='20190501BM') {
                    $row = $KftbmModel->where('KP_Phone',$data['KP_Phone'])->where("KP_Type",$data['KP_Type'])->find();
                    if ($row) {
                        return json(['info'=>'你的手机号已领过优惠']);
                    }
                }
                $result = $KftbmModel->allowField(true)->save($data);
                $this->doRequest($_SERVER['HTTP_HOST'],'/index.php/ajax/SendEmail',array(
                    'title'=>$data['KP_Title'],
                    'name'=>$data['KP_Name'],
                    'phone'=>$data['KP_Phone'],
                    'source'=>$data['KP_Url'],
                    'content'=>$data['KP_Message'],
                    'call_back_rul'=>''
                    )
                );
                if ($result !== false) {
                    return json(['info'=>'Yes']);
                    
                } else {
                    return json(['info'=>$KftbmModel->getError()]);
                }

                
            } catch (\think\exception\PDOException $e) {
                return json(['info'=>$e->getMessage()]);
            } catch (\think\Exception $e) {
                return json(['info'=>$e->getMessage()]);
            }
        }
   }

   public function SendEmail(){
        $params = $this->request->post();
        $name = $params['name'];
        $phone=$params['phone'];
        $source=$params['source'];
        $content=$params['content'];
        $title = $params['title'];
        $contents = "客户:".$name."<br>"."联系电话:".$phone."<br>"."来源页面:".$source."<br>"."内容:".$content;
        $email = new Email;
        $email->subject($title)->to(config('site.receive'))->message($contents)->send();
   }

   public function doRequest($host,$path, $param=array()){

        $query = isset($param)? http_build_query($param) : ''; 
 
        $port = 80; 
        $errno = 0; 
        $errstr = ''; 
        $timeout = 10; 
        $fp = fsockopen($host, $port, $errno, $errstr, $timeout); 
        if (!$fp) {
            echo "请求失败";exit;
        }
        $out = "POST ".$path." HTTP/1.1\r\n"; 
        $out .= "host:".$host."\r\n"; 
        $out .= "content-length:".strlen($query)."\r\n"; 
        $out .= "content-type:application/x-www-form-urlencoded\r\n"; 
        $out .= "connection:close\r\n\r\n"; 
        $out .= $query; 
        fwrite($fp, $out);
        //输出请求结果（测试时用）
          /*  $receive = '';
            while (!feof($fp)) 
            {
              $receive .= '<br>'.fgets($fp, 128);
            }
            echo "<br />".$receive;*/
        fclose($fp); 
    }
}
