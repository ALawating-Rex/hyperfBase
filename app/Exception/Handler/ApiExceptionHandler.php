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

namespace App\Exception\Handler;

use App\Constants\StatusCode;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use App\Util\Response;
use App\Exception\ApiException;

class ApiExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if($throwable instanceof ApiException){
            // 阻止异常冒泡
            $this->stopPropagation();
            // 格式化输出
            $data = Response::error($throwable->getCode(), $throwable->getMessage());
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8')->withStatus(200)->withBody(new SwooleStream(json_encode($data,JSON_UNESCAPED_UNICODE)));
        }
        // 交给下一个异常处理器
        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
