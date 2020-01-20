# PHP并发IO编程之路
* 并发 IO 问题一直是服务器端编程中的技术难题，从最早的同步阻塞直接 Fork 进程，到 Worker 进程池/线程池，到现在的异步IO、协程。PHP 程序员因为有强大的 LAMP 框架，对这类底层方面的知识知之甚少，本文目的就是详细介绍 PHP 进行并发 IO 编程的各种尝试。
 ### 1、socket 编程
 ### 2、多进程 实现socket编程
 ### 3 、 select  
 ### 4, poll 
 ### 5、 epoll （使用libevent 扩展，实现 epoll I/O 多路复用）
 
#### event 扩展安装 

以下测试是在unbantu系统，php7.2
php7.2 安装event扩展

1、拉去docker php7.2镜像
docker pull phpdockerio/php72-fpm （或者直接拉取我的镜像 ： ）
//挂载目录
docker run --name  myphp72-fpm -v /Users/zhuliubao:/www/php-project  -d phpdockerio/php72-fpm:latest
// 进入容器
docker exec -it myphp72-fpm bash 

2、 安装event前，确保已经安装sockets 扩展

通过php -m |grep sockets 查看是否安装

如果没有安装sockets扩展，在安装event时，会报如下错误
onfigure: error: Couldn't find /usr/local/include/php/sockets/php_sockets.h. Please check if sockets extension installed


3、安装event扩展依赖的libevent-dev包，命令行运行   
apt-get install libevent-dev -y

4、 安装event扩展
pecl install event

注意提示：Include libevent OpenSSL support [yes] : 时输入no回车，
注意提示：PHP Namespace for all Event classes :时输入yes，其它直接敲回车就行


可能碰到问题：

1、ubuntu~ phpize: command not found
解决办法：
sudo apt-get install php-dev
or ：sudo apt-get install php7.2-dev

 * 说明 可以使用，已经 打包镜像 (docker push zlbonlydocker/php72-fpm:tagname
)
