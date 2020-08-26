<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\HttpServer\Router\Router;
use App\Middleware\Auth\AuthMiddleware;
use App\Middleware\Auth\AuthSimpleMiddleware;

// 不需要登录
Router::addGroup('/api',function (){
    Router::addGroup('/user',function (){
        Router::addRoute(['GET', 'POST'], '/login', 'App\Controller\Api\User\UserController@userLogin'); // 用户登录接口
    });
});

// 可以不登录， 登录了则获取到用户信息
Router::addGroup('',function (){
    Router::addGroup('/api',function (){
        Router::addGroup('/product',function (){
            // TODO 举例：AuthSimpleMiddleware 中间件应用场景：登录了根据登录用户返回产品价格，未登录返回产品市场价
            Router::addRoute(['GET', 'POST'], '/info', 'App\Controller\Api\Product\ProductController@userAdd'); // 产品详情
        });
    });
},['middleware' => [AuthSimpleMiddleware::class]]);

// 需要登录
Router::addGroup('',function (){
    Router::addGroup('/admin',function (){
        Router::addGroup('/user',function (){
            Router::addRoute(['GET', 'POST'], '/add', 'App\Controller\Admin\User\UserController@userAdd'); // 用户增加
            Router::addRoute(['GET', 'POST'], '/delete', 'App\Controller\Admin\User\UserController@userDelete'); // 用户删除
            Router::addRoute(['GET', 'POST'], '/update', 'App\Controller\Admin\User\UserController@userUpdate'); // 用户修改
            Router::addRoute(['GET', 'POST'], '/info', 'App\Controller\Admin\User\UserController@userInfo'); // 用户详情
            Router::addRoute(['GET', 'POST'], '/list', 'App\Controller\Admin\User\UserController@userList'); // 用户列表
        });
    });

    Router::addGroup('/api',function (){
        Router::addGroup('/user',function (){
            Router::addRoute(['GET', 'POST'], '/info', 'App\Controller\Api\User\UserController@userInfo'); // 用户详情
        });
    });
},['middleware' => [AuthMiddleware::class]]);
