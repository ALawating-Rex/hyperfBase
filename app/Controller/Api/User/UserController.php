<?php

declare(strict_types=1);

namespace App\Controller\Api\User;

use App\Model\User;
use App\Util\Log;
use App\Util\Response;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use App\Constants\StatusCode;
use Phper666\JWTAuth\JWT;

class UserController
{
    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;
    /**
     * @Inject
     *
     * @var JWT
     */
    protected $jwt;

    public function userLogin(RequestInterface $request, ResponseInterface $response)
    {
        $validator = $this->validationFactory->make(
            $request->all(),
            [
                'username' => 'required|string',
                'password' => 'required|string',
            ]
        );
        if ($validator->fails()){
            return Response::error(StatusCode::PARAM_ERROR,'',$validator);
        }

        $username = $request->input('username');
        $password = $request->input('password');

        $objUser = User::where('username',$username)->first();
        if(empty($objUser) || !password_verify($password,$objUser->password)){
            return Response::error(StatusCode::USER_LOGIN_FAILED);
        }

        $userData = [
            'hb_user' => [
                'uid' => $objUser->id,
                'name' => $objUser->name,
                'username' => $objUser->username,
                'role' => $objUser->role,
            ]
        ];
        $token = $this->jwt->getToken($userData);
        $data = [
            'tokenHead' => $this->jwt->tokenPrefix.' ',
            'token' => $token,
            'exp'   => $this->jwt->getTTL(),
        ];
        return Response::success($data);
    }

    public function userInfo(RequestInterface $request, ResponseInterface $response)
    {
        $user = $request->getAttribute('hb_user');
        Log::debug('获取用户信息： ',$user);
        $objUser = User::where('id',$user['uid'])->first();
        if(empty($objUser)){
            return Response::error(StatusCode::ACCOUNT_NOT_EXISTS);
        }

        $data = $objUser->toArray();
        return Response::success($data);
    }
}
