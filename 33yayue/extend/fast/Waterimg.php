<?php

namespace fast;

/**
 * 中文转拼音类
 */
class Waterimg
{


    /** 封装可以制作不同位置的水印图的函数
     * string $src_image,原图路径
     * string $water_image,水印图路径
     * string $path,水印图保存位置
     * string &$error,错误代码
     * int $position = 1,水印图加的位置,1代表左上角,9代表右下角
     * int $pct = 20, 透明度
    **/
    static function make_water_mark($src_image, $water_image, $path,$filename, &$error, $position, $pct) {
        // 验证原图资源和水印图都存在
        if (!is_file($src_image)) {
            $error = '原图不存在';
            return false;
        }
        if (!is_file($water_image)) {
            $error = '水印图不存在';
            return false;
        }
 
        // 判断路径保存是否存在
        if (!is_dir($path)) {
            $error = '保存位置不正确';
            return false;
        }
 
        // 确认图片格式,选择适当函数
        $src_info = getimagesize($src_image);
        $water_info = getimagesize($water_image);
        // echo $src_info['mime'];
        $allow = array(
            'image/jpeg' => 'jpeg',
            'image/gif' => 'gif',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/pjpeg' => 'jpeg'
        );
 
        // 匹配数据
        if (!array_key_exists($src_info['mime'], $allow)) {
            $error = "当前文件资源不允许制作水印图";
            return false;
        }
        if (!array_key_exists($water_info['mime'], $allow)) {
            $error = "当前水印图资源不允许制作使用";
            return false;
        }
 
 
        // 组合函数
        $src_open = 'imagecreatefrom'.$allow[$src_info['mime']];
        $water_open = 'imagecreatefrom'.$allow[$water_info['mime']];
        $src_save = 'image'.$allow[$src_info['mime']];
 
        // 打开图片资源
        $src = $src_open($src_image);
        $water = $water_open($water_image);
 
        // 合并图片资源,产生水印
        // 首先计算水印图在原图中出现的位置
        $start_x = $start_y = 0;
        switch ($position) {
            case 1:
                break;
            case 2:
                $start_x = floor(($src_info[0] - $water_info[0])/2);
                break;
            case 3:
                $start_x = $src_info[0] - $water_info[0];
                break;
            case 4:
                $start_y = floor(($src_info[1] - $water_info[1])/2);
                break;
            case 5:
                $start_x = floor(($src_info[0] - $water_info[0])/2);
                $start_y = floor(($src_info[1] - $water_info[1])/2);
                break;
            case 6:
                $start_x = $src_info[0] - $water_info[0];
                $start_y = floor(($src_info[1] - $water_info[1])/2);
                break;
            case 7:
                $start_y = $src_info[1] - $water_info[1];
                break;
            case 8:
                $start_x = floor(($src_info[0] - $water_info[0])/2);
                $start_y = floor(($src_info[1] - $water_info[1])/2);
                break;
            case 9:
                $start_x = $src_info[0] - $water_info[0];
                $start_y = $src_info[1] - $water_info[1];
                break;
        }
        // 合并图片资源,产生水印
        if (imagecopymerge($src, $water, $start_x, $start_y, 0, 0, $water_info[0], $water_info[1], $pct)) {
            // 成功,保存图片
             header('Content-type:'.$src_info['mime']);
            $imagename = 'watermark_'.$src_image;
            //$src_save($src, $path.'/watermark_'.$src_image);
            list($dst_w, $dst_h, $dst_type) = getimagesize($src_image);

            switch ($dst_type) {
                case 1://GIF
                    imagegif($src,$path.$filename);
                    break;
                case 2://JPG
                    imagejpeg($src,$path.$filename,97);
                    break;
                case 3://PNG
                    imagepng($src,$path.$filename,9);
                    break;
                default:
                    break;

            }
            // 销毁资源
            imagedestroy($src);
            imagedestroy($water);
            return $imagename;
        } else {
            // 失败
            echo "水印图制作失败";
            return false;
        }
 
    }


    /** 封装可以制作不同位置的水印图的函数
     * string $src_image,原图路径
     * string $water_image,水印图路径
     * string $path,水印图保存位置
     * string &$error,错误代码
     * int $position = 1,水印图加的位置,1代表左上角,9代表右下角
     * int $pct = 20, 透明度
    **/
    static function make_water_text($src_image, $water_text, $path,$filename, &$error, $position, $pct) {
        // 验证原图资源和水印图都存在
        if (!is_file($src_image)) {
            $error = '原图不存在';
            return false;
        }
 
        // 判断路径保存是否存在
        if (!is_dir($path)) {
            $error = '保存位置不正确';
            return false;
        }
 
        // 确认图片格式,选择适当函数
        $src_info = getimagesize($src_image);
        $allow = array(
            'image/jpeg' => 'jpeg',
            'image/gif' => 'gif',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/pjpeg' => 'jpeg'
        );
 
        // 匹配数据
        if (!array_key_exists($src_info['mime'], $allow)) {
            $error = "当前文件资源不允许制作水印图";
            return false;
        }
        // 组合函数
        $src_open = 'imagecreatefrom'.$allow[$src_info['mime']];
 
        // 打开图片资源
        $src = $src_open($src_image);

        //打上文字
        $font = './simsun.ttc';//字体
        $black = imagecolorallocate($src, 0x00, 0x00, 0x00);//字体颜色
        
        // 合并图片资源,产生水印
        // 首先计算水印图在原图中出现的位置
        $start_x = $start_y = 20;
        switch ($position) {
            case 1:
                break;
            case 2:
                $start_x = floor(($src_info[0])/2);
                break;
            case 3:
                $start_x = $src_info[0];
                break;
            case 4:
                $start_y = floor(($src_info[1])/2);
                break;
            case 5:
                $start_x = floor(($src_info[0])/2);
                $start_y = floor(($src_info[1])/2);
                break;
            case 6:
                $start_x = $src_info[0];
                $start_y = floor(($src_info[1])/2);
                break;
            case 7:
                $start_y = $src_info[1]-20;
                break;
            case 8:
                $start_x = floor(($src_info[0])/2);
                $start_y = floor(($src_info[1])/2);
                break;
            case 9:
                $start_x = $src_info[0]-80;
                $start_y = $src_info[1]-20;
                break;
        }
        imagefttext($src, 15, 0, $start_x, $start_y, $black, $font, $water_text);
        //输出图片
        list($dst_w, $dst_h, $dst_type) = getimagesize($src_image);
        switch ($dst_type) {
            case 1://GIF
                header('Content-Type: image/gif');
                imagegif($src,$path.$filename);
                break;
            case 2://JPG
                header('Content-Type: image/jpeg');
                imagejpeg($src,$path.$filename,97);
                break;
            case 3://PNG
                header('Content-Type: image/png');
                imagepng($src,$path.$filename,9);
                break;
            default:
                break;
        }
 
    }


}
