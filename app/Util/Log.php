<?php

namespace App\Util;

use Hyperf\Utils\Context;
use Hyperf\Logger\Logger;
use Hyperf\Utils\ApplicationContext;
use Hyperf\HttpServer\Contract\RequestInterface;

class Log
{
    public static function get(string $name = 'app', string $key = 'default')
    {
        return ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get($name,$key);
    }

    // TODO 修改log 记录方式 - 例如使用统一的 log 服务
    public static function common(string $level, $msg, array $extra = [], $name = 'app', $key= 'default')
    {
        $request = Context::get(RequestInterface::class);
        $hbRequestId = $request->getAttribute('hb_request_id');
        $extra = array_merge($extra,['extra' => ['hb_request_id' => $hbRequestId]]);

        $notLogLevel = config('constants.NOT_LOG_ARR');
        $notLogLevelArr = explode(',',$notLogLevel);
        if(in_array($level,$notLogLevelArr)){
        }else{
            $log = self::get($name,$key);
            if(is_string($msg)){
            }elseif (is_array($msg)){
                $msg = json_encode($msg);
            }elseif (is_object($msg)){
                $msg = var_export($msg,true);
            }else{}

            $log->$level($msg,$extra);
        }
    }

    public static function debug($msg, array $extra = [], $name = 'app', $key = 'default')
    {
       return self::common('debug',$msg, $extra, $name, $key);
    }

    public static function info($msg, array $extra = [], $name = 'app', $key= 'default')
    {
        return self::common('info',$msg, $extra, $name, $key);
    }

    public static function notice($msg, array $extra = [], $name = 'app', $key= 'default')
    {
        return self::common('notice',$msg, $extra, $name, $key);
    }

    public static function warning($msg, array $extra = [], $name = 'app', $key= 'default')
    {
        return self::common('warning',$msg, $extra, $name, $key);
    }

    public static function error($msg, array $extra = [], $name = 'app', $key= 'default')
    {
        return self::common('notice',$msg, $extra, $name, $key);
    }

    public static function critical($msg, array $extra = [], $name = 'app', $key= 'default')
    {
        return self::common('critical',$msg, $extra, $name, $key);
    }
}
