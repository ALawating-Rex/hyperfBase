# 基于docker 安装 hyperf + 远程断点调试 （超详细）

## 安装

1. 首先拉取 docker 镜像  
`docker pull hyperf/hyperf`

2. 启动容器  
`docker run -dit --name hyperf1 -v /var/www/html/hyperf_study:/hyperf-skeleton -p 9501:9501 -p 9502:9502 -p 9503:9503 -p 9504:9504 --entrypoint /bin/sh --link mysql1:mysql --link redis1:redis hyperf/hyperf`

### 注意:
-v 来挂载文件  
-p 映射端口，初始只映射一个端口也是可以的， 我为了后面好扩展所以映射了多个
--link 链接了我自己的 mysql 容器 和 redis 容器（这两个docker 容器的搭建这里就不展开了）

3. 进入容器  
`docker exec -it hyperf1 /bin/bash`
4. 安装composer 安装 hyperf 并启动  
`wget https://github.com/composer/composer/releases/download/1.9.0/composer.phar`  
// 修改为可执行  
`chmod u+x composer.phar`  
// 复制到/usr/local/bin/ 这样就可以直接运行composer 命令  
`mv composer.phar /usr/local/bin/composer`  
// 修改仓库地址为阿里云  
`composer config -g repo.packagist composer https://mirrors.aliyun.com/composer`  
// 通过 Composer 安装 hyperf/hyperf-skeleton 项目  
`composer create-project hyperf/hyperf-skeleton`  
// 进入安装好的 Hyperf 项目目录  
`cd hyperf-skeleton`  
// 启动 Hyperf  
`php bin/hyperf.php start`  

5. 访问  
现在你就可以访问 http://ip:9051 来查看了

## 断点调试

1. 安装 sdebug （本地使用 phpstorm）   
```
1.git clone https://github.com/swoole/sdebug.git -b sdebug_2_9 --depth=1
2.apk add php7-dev
3.phpize
4.apk add gcc
5.apk add g++
6.apk add make
7../configure
8.make clean
9.make
10.make install

11. 配置 php.ini (/etc/php7/php.ini)
[xdebug]
zend_extension="xdebug.so"
xdebug.idekey=PHPSTORM
xdebug.remote_enable = 1
xdebug.remote_connect_back = On
xdebug.remote_autostart = true
xdebug.remote_host = 本地的ip(指的是编写代码的那台电脑)
xdebug.remote_port = 9550(本地的端口)

```
2. 查看安装结果  
`php --ri sdebug`

3. 配置 PHPStorm  
<img src="https://raw.githubusercontent.com/ALawating-Rex/doc_asset/master/docker_hyperf/phpstorm_debug1.png"></img>  
注意这里的端口要和 php.ini 里配置的一致    
<img src="https://raw.githubusercontent.com/ALawating-Rex/doc_asset/master/docker_hyperf/phpstorm_servers1.png"></img>  
192.168.1.11 和 9501 是虚拟机暴露出来的端口， 下面的路径 File/Directory 是本地代码路径， Absolute path on the server 是docker里hyperf的路径
上面挂载的是 /hyperf-skeleton 所以这里写这个即可  
接下来在 docker容器里运行：  
`export PHP_IDE_CONFIG=serverName=虚拟机11_docker_hyperf3 // 这里的名字就是server配置的name`      
点击phpstorm 工具栏的 Add Configuration 添加如下配置  
<img src="https://raw.githubusercontent.com/ALawating-Rex/doc_asset/master/docker_hyperf/run_debug1.png"></img>    
IDE key 是 php.ini 里配置的： xdebug.idekey  
最后让 PHPStorm 监听运行即可  
<img src="https://raw.githubusercontent.com/ALawating-Rex/doc_asset/master/docker_hyperf/run_debug2.png"></img> 

此时你再次进入 docker 执行 
`php bin/hyperf.php start`  
就会发现 PHPStorm 已经断到程序了
