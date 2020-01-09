<?php

/** 使用多进程和多线程实现 服务器端**/
//创建服务端的socket套接流,net协议为IPv4，protocol协议为TCP
$socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);

/*绑定接收的套接流主机和端口,与客户端相对应*/
if(socket_bind($socket,'127.0.0.1',8888) == false) {
    echo 'server bind fail '.socket_strerror(socket_last_error());
}

//监听套接流
if(socket_listen($socket,4) == false) {
    echo 'server listen fail' .socket_strerror(socket_last_error());
}


while(1){
    /*接收客户端传过来的信息*/
    $accept_resource = socket_accept($socket);

    $clients[] = $accept_resource;

    echo "client connect succ. fd: ".$accept_resource."\n";

    //获取客户端IP地址
    socket_getpeername($accept_resource, $addr, $port);
    echo "client addr: $addr:$port\n";

    //获取服务端IP地址
    socket_getsockname($accept_resource, $addr, $port);
    echo "server addr: $addr:$port\n";

    echo "total: ".(count($clients))." client\n";

    if(pcntl_fork() == 0 ) { // 每个客户端链接时开启一个进程
        if($accept_resource !== false) {
            socket_getpeername($accept_resource,$ip,$port);

            $id = posix_getpid();
            echo("进程ID:$id == 客户端:".$ip.":".$port."已连接\r\n");


            while(true ) {
                $messge = socket_read($accept_resource,1024);
                echo 'server receive is '.$messge.PHP_EOL;
                if(mb_strlen($messge) != 'quit') {
                    $return_client = "server response is : I'm fine  thank you!" ."\n";
                    $return_client = mb_convert_encoding($return_client,'GBK','UTF-8');
                    /*向socket_accept的套接流写入信息，也就是回馈信息给socket_bind()所绑定的主机客户端*/
                    socket_write($accept_resource,$return_client,strlen($return_client));

                }else {
                    echo 'scoket_read is fail';
                    socket_close($accept_resource);
                    exit();
                }
            }

        }
    }
}

socket_close($socket);

