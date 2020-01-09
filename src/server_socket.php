<?php

/** 服务器端**/
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


do {
    /*接收客户端传过来的信息*/
    $accept_resource = socket_accept($socket);
    if($accept_resource !== false) {
        $messge = socket_read($accept_resource,1024);
        echo 'server receive is '.$messge.PHP_EOL;
        if($messge !== '') {
            $return_client = "server receive is : I'm fine  thank you!";
            $return_client = mb_convert_encoding($return_client,'GBK','UTF-8');

            /*向socket_accept的套接流写入信息，也就是回馈信息给socket_bind()所绑定的主机客户端*/
            socket_write($accept_resource,$return_client,strlen($return_client));
        }else {
            echo 'scoket_read is fail';
        }

        socket_close($accept_resource);
    }

}while(true);

socket_close($socket);

