<?php

namespace app\mobile\controller;

use app\common\controller\Frontend;
use think\Lang;
use app\common\library\Email;
use app\admin\model\Kftbm as KftbmModel;
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
                $result = $KftbmModel->allowField(true)->save($data);
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
        /*$params = $this->request->post();
        $data['KP_Type'] = $params['T_Type'];
        $data['KP_Name'] = $params['T_KftName'];
        $data['KP_Phone'] = $params['T_KftPhone'];
        $data['KP_Title'] = $params['T_Title'];
        $data['KP_Message'] = $params['T_Message'];
        $data['KP_HouseID'] = $params['T_LpID'];
        $data['KP_TgID'] = $params['T_TgID'];
        $data['KP_Url'] = $params['T_PageUrl'];//$request->url();
        $data['KP_BmTime'] = date("Y-m-d H:i:s");*/
        $email = new Email;
        $email->subject('这里是邮件标题')->to('2439422746@qq.com')->message('这里是邮件正文')->send();
   }

}
