<?php

declare(strict_types=1);

namespace App\Middleware\Auth;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Util\Log;
use App\Util\Response;
use App\Constants\StatusCode;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\Utils\Context;
use Phper666\JWTAuth\JWT;


class JwtAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;

    /**
     * @var JWT
     */
    protected $jwt;

    public function __construct(ContainerInterface $container, HttpResponse $response, RequestInterface $request, JWT $jwt)
    {
        $this->container = $container;
        $this->response = $response;
        $this->request = $request;
        $this->jwt = $jwt;
    }

    // 只适用于 jwt 验证
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 登录接口不需要验证
        if(stripos($this->request->path(),'login') !== false){
            return $handler->handle($request);
        }
        $appEnv = config('constants.APP_ENV');
        $appDebug = config('constants.APP_DEBUG');
        $token = $request->getHeader('Authorization')[0] ?? '';
        Log::debug('获取 token --- '.$token);
        $isValidToken = false;
        if(empty($token) && $appEnv != 'prod' && $appDebug == 2){
            $hbUser = [
                'uid' => 1,
                'name' => '测试用户1',
                'username' => 'testuser1',
                'role' => 10,
            ];
            $request = $request->withAttribute('hb_user', $hbUser);
            Context::set(RequestInterface::class, $request);

            return $handler->handle($request);
        }else{
            try {
                if ($this->jwt->checkToken()) {
                    $jwtData = $this->jwt->getParserData();
                    //Log::debug($jwtData['hb_user'],["获取 hb_user"]);
                    if(isset($jwtData['hb_user']) && !empty($jwtData['hb_user'])){
                        $isValidToken = true;
                        $hbUser =  json_decode(json_encode($jwtData['hb_user']),true);
                    }
                }
            } catch(\Exception $e) {
                $rtn = Response::makeJsonResponse();
                Response::setErr($rtn, StatusCode::USER_NOT_LOGIN);
                return Response::json($this->response,$rtn);
            }
        }

        if  ($isValidToken)  {
            //更改上下文，写入用户信息
            $request = $request->withAttribute('hb_user', $hbUser);
            Context::set(RequestInterface::class, $request);

            return $handler->handle($request);
        }

        $rtn = Response::makeJsonResponse();
        Response::setErr($rtn, StatusCode::USER_NOT_LOGIN);
        return Response::json($this->response,$rtn);
    }
}