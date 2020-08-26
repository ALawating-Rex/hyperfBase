<?php

namespace App\Util;

use App\Constants\StatusCode;
use Hyperf\HttpServer\Contract\ResponseInterface;

class Response
{
    public static function makeJsonResponse(): array
    {
        return array(
            'status' => StatusCode::SUCCESS,
            'msg' => StatusCode::getMessage(StatusCode::SUCCESS),
            'data' => (object)[],
            'log_id' => config('constants.APP_NAME') . ' :: ' . mt_rand(1000000, 2147483647),
        );
    }

    public static function error($statusCode, $extra = '', $validator = [])
    {
        $rtn = self::makeJsonResponse();
        if (!empty($validator) && config('constants.APP_ENV') != 'prod' && $statusCode == StatusCode::PARAM_ERROR) {
            $firstError = $validator->errors()->first();
            if (!empty($firstError)) {
                $extra .= ' :: error :: ' . $firstError;
            }
        }
        $rtn['status'] = $statusCode;
        if ($extra != '' && is_string($extra)) {
            $rtn['msg'] = StatusCode::getMessage($statusCode) . $extra;
        } else {
            $rtn['msg'] = StatusCode::getMessage($statusCode);
        }
        $rtn['data'] = (object)[];
        Log::debug($rtn,['-响应-']);
        return $rtn;
    }

    public static function setErr(&$rtn, $statusCode, $extra = '', $validator = [])
    {
        if (!empty($validator) && config('constants.APP_ENV') != 'prod' && $statusCode == StatusCode::PARAM_ERROR) {
            $firstError = $validator->errors()->first();
            if (!empty($firstError)) {
                $extra .= ' :: error :: ' . $firstError;
            }
        }
        $rtn['status'] = $statusCode;
        if ($extra != '' && is_string($extra)) {
            $rtn['msg'] = StatusCode::getMessage($statusCode) . $extra;
        } else {
            $rtn['msg'] = StatusCode::getMessage($statusCode);
        }
        $rtn['data'] = (object)[];

        return true;
    }

    public static function setErrSimple($statusCode, $extra = '', $validator = [])
    {
        $rtn = self::makeJsonResponse();
        if (!empty($validator) && config('constants.APP_ENV') != 'prod' && $statusCode == StatusCode::PARAM_ERROR) {
            $firstError = $validator->errors()->first();
            if (!empty($firstError)) {
                $extra .= ' :: error :: ' . $firstError;
            }
        }
        $rtn['status'] = $statusCode;
        if ($extra != '' && is_string($extra)) {
            $rtn['msg'] = StatusCode::getMessage($statusCode) . $extra;
        } else {
            $rtn['msg'] = StatusCode::getMessage($statusCode);
        }
        $rtn['data'] = (object)[];

        return true;
    }

    public static function success($data = [])
    {
        $rtn = self::makeJsonResponse();
        if(!empty($data)){
            $rtn['data'] = $data;
        }else{
            $rtn['data'] = (object)[];
        }
        Log::debug($rtn,['-响应-']);
        return $rtn;
    }

    public static function json(ResponseInterface $response, $rtn)
    {
        if(empty($rtn['data'])){
            $rtn['data'] = (object)[];
        }
        Log::debug($rtn,['-响应-']);
        return $response->json($rtn);
    }

    // 格式化服务响应的数据，同时返回
    public static function handleSrvRspSimple(array $srvRsp)
    {
        $rtn = self::makeJsonResponse();
        if(config('constants.APP_ENV') != 'prod' && isset($srvRsp['dev_msg'])){
            $devMsg = $srvRsp['dev_msg'];
            Log::debug($devMsg);
        }else{
            $devMsg = '';
        }
        if($srvRsp['status'] == StatusCode::CUSTOM_ERROR){
            self::setErr($rtn,StatusCode::CUSTOM_ERROR,$srvRsp['msg'].$devMsg);
        }elseif (!is_integer($srvRsp['status']) && isset($srvRsp['msg'])){
            self::setErr($rtn,StatusCode::CUSTOM_ERROR,$srvRsp['msg'].$devMsg);
        } elseif ($srvRsp['status'] != StatusCode::SUCCESS){
            self::setErr($rtn,$srvRsp['status'],$devMsg);
        }else{
            $rtn['data'] = $srvRsp['data'] ?? (object)[];
        }

        return $rtn;
    }

    // 格式化服务响应的数据
    public static function handleSrvRsp(&$rtn, array $srvRsp)
    {
        if(config('constants.APP_ENV') != 'prod' && isset($srvRsp['dev_msg'])){
            $devMsg = $srvRsp['dev_msg'];
            Log::debug($devMsg);
        }else{
            $devMsg = '';
        }
        if($srvRsp['status'] == StatusCode::CUSTOM_ERROR){
            self::setErr($rtn,StatusCode::CUSTOM_ERROR,$srvRsp['msg'].$devMsg);
        }elseif (!is_integer($srvRsp['status']) && isset($srvRsp['msg'])){
            self::setErr($rtn,StatusCode::CUSTOM_ERROR,$srvRsp['msg'].$devMsg);
        } elseif ($srvRsp['status'] != StatusCode::SUCCESS){
            self::setErr($rtn,$srvRsp['status'],$devMsg);
        }else{
            $data = json_decode(json_encode($rtn['data']),true);
            $tmpData = $srvRsp['data'] ?? [];
            $rtn['data'] = empty($data) ? $tmpData : array_merge($data,$tmpData);
        }

        return $rtn;
    }

}
