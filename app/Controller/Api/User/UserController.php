<?php

declare(strict_types=1);

namespace App\Controller\Api\User;

use App\Exception\ApiException;
use App\Model\User;
use App\Util\Log;
use App\Util\Response;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use App\Constants\StatusCode;
use Phper666\JWTAuth\JWT;
use Donjan\Permission\Models\Permission;
use Donjan\Permission\Models\Role;

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

    public function userPermission(RequestInterface $request, ResponseInterface $response)
    {
        $user = User::where('id',1)->first();
        throw new ApiException(StatusCode::ACCOUNT_NOT_EXISTS,'- 自定义错误');
        if($user->can('user-center/user/get')){
            return Response::success();
        }else{
            return 'can not';
        }

        //创建一个角色
        $role = Role::create(['name' => '管理员','description'=>'管理员描述']);
        //创建权限
        $permission1 = Permission::create(['name' => 'user-center/user/get','display_name'=>'用户管理','url'=>'user-center/user']);
        $permission2 = Permission::create(['name' => 'user-center/user/post','display_name'=>'创建用户','parent_id'=>$permission1->id]);
        //为角色分配一个权限
        $role->givePermissionTo($permission2);
        //$role->syncPermissions($permissions);//多个
        //$role->syncPermissions([1,2,3]);
        //权限添加到一个角色
        $permission1->assignRole($role);
        $permission2->syncRoles([$role]);//多个
        //$permission->syncRoles([1,2,3]);
        //删除权限
        //$role->revokePermissionTo($permission2);
        //$permission1->removeRole($role);
        //为用户直接分配权限
        $user = User::find(1);
        $user->givePermissionTo('user-center/user/get');
        //为用户分配角色
        $user->assignRole('管理员');
    }
}
