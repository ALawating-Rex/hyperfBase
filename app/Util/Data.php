<?php

namespace App\Util;

class Data
{

    /**
     * 转换静态文件的URL
     * @param string $path
     * @return string
     */
    public static function transformAssetUrl($path = ''){
        $rootUrl = config('constants.ASSET_URL');

        if(empty($path)){
            // 获取根URl
            return '';
        }else{
            if(stripos($path,'http:') === 0 || stripos($path,'https:') === 0){
                // 完整的URl
                return $path;
            }elseif(stripos($path,'/') === 0){
                // 路径以 / 开头
                return $rootUrl.$path;
            }else{
                // 路径不以 / 开头
                return $rootUrl.'/'.$path;
            }
        }
    }
}
