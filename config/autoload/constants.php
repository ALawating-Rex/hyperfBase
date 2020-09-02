<?php

declare(strict_types=1);

return [
    // jwtAuth or postAuth or headerAuth
    'AUTH_METHOD' => env('AUTH_METHOD','jwtAuth'), // 决定了验证用户的方式，具体使用的地方参考 - AuthMiddleware
    'APP_NAME' => env('APP_NAME','hyperfBase'),
    'APP_ENV' => env('APP_ENV','local'),
    'APP_DEBUG' => env('APP_DEBUG',1), // 0-不开启DEBUG(暂时无用) 1-开启DEBUG(暂时无用) 2-开启DEBUG，且验证用户的地方返回默认用户 参考 - AuthMiddleware
    'ASSET_URL' => env('ASSET_URL','https://static.l.com'),
];
