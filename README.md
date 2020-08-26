## About hyperfBase

基于 Hyperf 搭建的基础通用框架，包含了必要的鉴权中间件，写好了用户业务逻辑以供参考。

### MiddleWare 说明
- requestMiddleware 全局中间件，记录请求相关的参数到 log
- AuthMiddleware 鉴权中间件，必须登录才允许通过，配置环境变量 AUTH_METHOD 来应用不同的鉴权方式。 （具体说明参考： config/autoload/constants.php 的注释）
验证通过会在 request 中添加 attribute ： hb_user ， controller里获取登录用户信息代码： `$user = $request->getAttribute('hb_user');`
- AuthSimpleMiddleware 鉴权中间件，基本和 AuthMiddleware 逻辑一致，不同点在于即使认证不通过不影响执行，只不过是 $request->getAttribute('hb_user'); 为空数组
- JwtAuthMiddleware 单独提出来 jwt 认证， 你可以添加修改自己的逻辑

### Util 说明
- Log 更简单的记录 log 
- Response 格式化输出，并且做了 log记录

## 安装步骤

1. composer install
2. cp .env.example .env
3. 根据实际修改 .env 配置的mysql 和 redis地址 
4. php bin/hyperf.php migrate

## 生产环境更新步骤

// 如果有新包 - composer require xxx 不建议 composer update
1. cp .env.example .env
2. 根据实际修改 .env 配置的mysql 和 redis地址 
3. php bin/hyperf.php migrate


## 说明

### 接口基础模版：
```
public function categoryList(RequestInterface $request, ResponseInterface $response)
    {
        //$rtn = Response::makeJsonResponse()
        $validator = $this->validationFactory->make(
            $request->all(),
            [
                'name' => 'required|string', // 筛选分类名称
                'page' => 'nullable|integer',
                'size' => 'nullable|integer',
            ]
        );
        if ($validator->fails()){
            //Response::setErr($rtn,StatusCode::PARAM_ERROR,'',$validator);
            //return Response::json($response,$rtn);
            return Response::error(StatusCode::PARAM_ERROR,'',$validator);
        }

        // 获取用户信息
        $user = $request->getAttribute('hb_user');
        Log::debug('获取用户信息： ',$user);
        $name = $request->input('name', '');
        $page = $request->input('page', 1);
        $size = $request->input('size', 20);

        Log::debug('参数：-----'.$name.'-'.$page.'-'.$size);

        $data = [
            'list' => [],
            'count' => 100
        ];
        return Response::success($data);
        //return Response::json($response,$rtn);
    }

```

### 生成 Model：
`php bin/hyperf.php gen:model table_name`

### 生成中间件：
`php ./bin/hyperf.php gen:middleware Auth/FooMiddleware`

### 数据迁移：
`php bin/hyperf.php gen:migration create_user_table`

### request 说明
```
request 中包含参数(通过 $request->getAttribute(key) 获取)：
hb_request_id - 表示每个请求的id 
hb_user - 表示发请求的用户的信息 
    [
        'id' => 1,
        'name' => 'aex',
        'username' => 'aex',
        'role' => 10,
    ]
```
### constants 说明
config/autoload/constants.php 里为一些配置变量做了说明，具体参考注释即可

## 编码规范约定
1. 变量使用驼峰模式
2. 函数必须做好注释
3. 每个 controller 应当按照模板书写，需要的变量提前初始化
4. 每个model 里如果有类似 status 、 type 这类字段 必须写明 1代表什么 2代表什么 。写到 model 注释里和database comment里

## TODO List
- 基于docker 安装 hyperf 的步骤
- 创建 initSeed - 初始化用户数据
- 完善单元测试，用户登录以及后续操作

