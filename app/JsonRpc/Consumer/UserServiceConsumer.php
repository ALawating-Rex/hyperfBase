<?php

namespace App\JsonRpc\Consumer;

use Hyperf\RpcClient\AbstractServiceClient;

class UserServiceConsumer extends AbstractServiceClient
{
    /**
     * 定义对应服务提供者的服务名称
     * @var string
     */
    protected $serviceName = 'UserService';

    /**
     * 定义对应服务提供者的服务协议
     * @var string
     */
    protected $protocol = 'jsonrpc-http';

    public function add(int $a, int $b)
    {
        return $this->__request(__FUNCTION__, compact('a', 'b'));
    }
}
