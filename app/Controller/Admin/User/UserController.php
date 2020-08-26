<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\Model\User;
use App\Util\Log;
use App\Util\Account;
use App\Util\Response;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use App\Constants\StatusCode;

class UserController
{
    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    public function userAdd(RequestInterface $request, ResponseInterface $response)
    {
        // TODO 可以结合翻译
        $validator = $this->validationFactory->make(
            $request->all(),
            [
                'name' => 'required|string',
                'username' => 'required|string',
                'phone' => 'required|string',
                'password' => 'nullable|string',
                'status' => 'nullable|integer',
                'role' => 'nullable|integer',
            ]
        );
        if ($validator->fails()){
            return Response::error(StatusCode::PARAM_ERROR,'',$validator);
        }

        $name = $request->input('name');
        $username = $request->input('username');
        $phone = $request->input('phone');
        $password = $request->input('password','aex.hyperfBase');
        $status = $request->input('status',1);
        $role = $request->input('role',1);

        // TODO 可以封装到 model 里
        $userExist = User::where('phone',$phone)->orWhere('username',$username)->first(['id']);
        if(!empty($userExist)){
            return Response::error(StatusCode::DATA_EXISTS);
        }

        $user = new User();
        $user->name = $name;
        $user->username = $username;
        $user->phone = $phone;
        $user->password = Account::makePassword($password);
        $user->status = $status;
        $user->role = $role;
        $user->save();

        return Response::success();
    }

    public function userDelete(RequestInterface $request, ResponseInterface $response)
    {
        $validator = $this->validationFactory->make(
            $request->all(),
            [
                'id' => 'required|integer',
            ]
        );
        if ($validator->fails()){
            return Response::error(StatusCode::PARAM_ERROR,'',$validator);
        }

        $userId = $request->input('id');
        user::where('id',$userId)->delete();

        return Response::success();
    }

    public function userUpdate(RequestInterface $request, ResponseInterface $response)
    {
        $validator = $this->validationFactory->make(
            $request->all(),
            [
                'id' => 'required|integer',
                'name' => 'required|string',
                'username' => 'required|string',
                'phone' => 'required|string',
                'password' => 'nullable|string',
                'status' => 'nullable|integer',
                'role' => 'nullable|integer',
            ]
        );
        if ($validator->fails()){
            return Response::error(StatusCode::PARAM_ERROR,'',$validator);
        }

        $userId = $request->input('id');
        $name = $request->input('name');
        $username = $request->input('username');
        $phone = $request->input('phone');
        $password = $request->input('password','aex.hyperfBase');
        $status = $request->input('status',1);
        $role = $request->input('role',1);

        $user = User::where('id',$userId)->first(['id']);
        if(empty($user)){
            return Response::error(StatusCode::DATA_NOT_EXISTS);
        }
        $userExist = User::where('id','!=',$userId)->where(function ($query) use ($phone,$username){
          $query->where('phone',$phone)->orWhere('username',$username);
        })->first(['id']);
        if(!empty($userExist)){
            return Response::error(StatusCode::DATA_EXISTS);
        }

        $user->username = $username;
        $user->name = $name;
        $user->phone = $phone;
        $user->password = Account::makePassword($password);
        $user->status = $status;
        $user->role = $role;
        $user->save();

        return Response::success();
    }

    public function userInfo(RequestInterface $request, ResponseInterface $response)
    {
        $validator = $this->validationFactory->make(
            $request->all(),
            [
                'id' => 'required|integer',
            ]
        );
        if ($validator->fails()){
            return Response::error(StatusCode::PARAM_ERROR,'',$validator);
        }

        $userId = $request->input('id');
        $user = User::where('id',$userId)->first();
        if(empty($user)){
            return Response::error(StatusCode::DATA_NOT_EXISTS);
        }
        $data = $user->toArray();

        return Response::success($data);
    }

    public function userList(RequestInterface $request, ResponseInterface $response)
    {
        $validator = $this->validationFactory->make(
            $request->all(),
            [
                'page' => 'nullable|integer',
                'size' => 'nullable|integer',
            ]
        );
        if ($validator->fails()){
            return Response::error(StatusCode::PARAM_ERROR,'',$validator);
        }
        $page = $request->input('page',1);
        $size = $request->input('size',20);
        $skip = ($page - 1) * $size;

        $userList = new User();
        $count = $userList->count();
        if($count == 0){
            $list = [];
            $data = [
                'count' => $count,
                'list' => $list,
            ];
            return Response::success($data);
        }else{
            $list = $userList->take($size)->skip($skip)->get();
        }

        //foreach ($list as $item){ // 单独处理字段 比如不需要展示 password字段， 比如有头像字段那么单独转换 }
        $data = [
            'count' => $count,
            'list' => $list->toArray(),
        ];
        return Response::success($data);
    }
}
