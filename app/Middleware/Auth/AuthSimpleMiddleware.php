<?php

declare(strict_types=1);

namespace App\Middleware\Auth;

use App\Util\Log;
use App\Util\Response;
use App\Constants\StatusCode;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Phper666\JWTAuth\JWT;
use Phper666\JWTAuth\Util\JWTUtil;

class AuthSimpleMiddleware implements MiddlewareInterface
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

    public function jwtAuth(): array
    {
        $appEnv = config('constants.APP_ENV');
        $appDebug = config('constants.APP_DEBUG');
        $token = $this->request->getHeader('Authorization')[0] ?? '';

        $isValidToken = false;
        if(empty($token) && $appEnv != 'prod' && $appDebug == 2){
            $hbUser = [
                'id' => 1,
                'name' => '测试用户1',
                'username' => 'testuser1',
                'role' => 10,
            ];

            return $hbUser;
        }else{
            try {
                if ($this->jwt->checkToken()) {
                    $jwtData = $this->jwt->getParserData();
                    if(isset($jwtData['hb_user']) && !empty($jwtData['hb_user'])){
                        $isValidToken = true;
                        $hbUser =  json_decode(json_encode($jwtData['hb_user']),true);
                    }
                }
            } catch(\Exception $e) {
                Log::debug('jwt verify error :'.$e->getMessage());
                return [];
            }
        }

        if($isValidToken){
            return $hbUser;
        }else{
            return [];
        }
    }

    public function headerAuth(): array
    {
        // TODO
    }

    public function postAuth(): array
    {
        $hbUser = $this->request->input('hb_user', []);
        if (empty($hbUser) || !isset($hbUser['id']) || !isset($hbUser['name']) || !isset($hbUser['role'])) {
            return [];
        }
        return $hbUser;
    }

    // 验证登录 存储用户信息
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authMethod = config('constants.AUTH_METHOD');
        $hbUserInfo = $this->$authMethod();
        $request = $request->withAttribute('hb_user', $hbUserInfo);
        Context::set(RequestInterface::class, $request);

        return $handler->handle($request);
    }
}