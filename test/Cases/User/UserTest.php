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
namespace HyperfTest\Cases\User;

use HyperfTest\HttpTestCase;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\ApplicationContext;

/**
 * @internal
 * @coversNothing
 */
class UserTest extends HttpTestCase
{

    public static $tokenHead;
    public static $token;

    public function testUserLogin()
    {
        $testData = [
            'username' => 'admin',
            'password' => 'aex.hyperfBase',
        ];
        $resp = $this->post('/api/user/login',$testData);
        $this->assertTrue(is_array($resp));
        $this->assertSame(200,$resp['status']);
        $this->assertArrayHasKey('token',$resp['data']);
        $this->assertArrayHasKey('tokenHead',$resp['data']);
        self::$tokenHead = $resp['data']['tokenHead'];
        self::$token = $resp['data']['token'];
    }

    public function testUserInfo()
    {
        if(config('constants.APP_DEBUG') != 2){
            echo PHP_EOL." \033[31m 建议设置 .env 文件的 APP_DEBUG=2 来测试需要登录的接口 \033[0m ".PHP_EOL;
        }

        // 如果不使用 APP_DEBUG=2 的方式，使用下面的方式模拟登录
        // $resp = $this->post('/api/user/info',[],['Authorization' => self::$tokenHead.self::$token]);

        $resp = $this->post('/api/user/info');
        $this->assertTrue(is_array($resp));
        $this->assertSame(200,$resp['status']);
    }
}
