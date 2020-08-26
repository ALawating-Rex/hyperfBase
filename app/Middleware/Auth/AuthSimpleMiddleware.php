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

    public function __construct(ContainerInterface $container, HttpResponse $response, RequestInterface $request)
    {
        $this->container = $container;
        $this->response = $response;
        $this->request = $request;
    }

    public function jwtAuth(): array
    {
        // TODO
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