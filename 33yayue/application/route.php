
<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
use think\Db;
Route::domain('yfadmin','admin');
Route::domain('api','api');
//$this->City = db('area')->where('domain',$_SERVER['HTTP_HOST'])->value('areaid');
$links = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$domain = $_SERVER['HTTP_HOST'];
$City = db('area')->where('domain',substr($domain,1,strlen($domain)))->value('areaid');
$City1 = db('area')->where('domain',$domain)->value('areaid');
$fir = explode('.', $domain);
if (substr($domain,0,1)=="m"&&$City||$fir[0]=="m") {
    /*if ($links==$domain.'/'&&$fir[0]=="m") {
        Route::domain('*','mobile/index/selectqy'); 
    }else{*/
        if ("33yayue.com"==$fir[1].'.'.$fir[2]) {
            Route::domain('*','mobile1'); 
        }else{
            Route::domain('*','mobile'); 
        }
        
    //}
    
}else{
    if (substr($domain,0,1)=="m"&&!$City1) {
        Route::domain('*','mobile');
    }else{
        Route::domain('*','index'); 
    }
}
Route::domain('www','index');
/*return [
    //别名配置,别名只能是映射到控制器且访问时必须加上请求的方法
    '__alias__'   => [
    ],
    //变量规则
    '__pattern__' => [
    ],
//        域名绑定到模块
        '__domain__'  => [
            'yfadmin' => 'admin',
            'api'   => 'api',
            'www' => 'index',
            '*.m' => 'mobile',
            '*' => 'index',
        ],
];*/
