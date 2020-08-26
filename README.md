## About hyperfBase

基于 Hyperf 搭建的基础通用框架

## 安装步骤

1. composer install
2. cp .env.example .env 
3. php bin/hyperf.php migrate

## 生产环境更新步骤

// 如果有新包 - composer require xxx 不建议 composer update
1. cp .env.example .env 
2. php bin/hyperf.php migrate


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
request 中包含参数(通过 $request->getAttribute(keey) 获取)：
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

## TODO 
+[ ] 基于docker 安装 hyperf 的步骤
+[ ] 创建 initSeed - 初始化用户数据
+[ ] 完善单元测试，用户登录以及后续操作

