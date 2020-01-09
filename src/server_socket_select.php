<?php
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if(!$socket) die("create server fail:".socket_strerror(socket_last_error())."\n");

//绑定
$ret = socket_bind($socket, "127.0.0.1", 8888);
if(!$ret) die("bind server fail:".socket_strerror(socket_last_error())."\n");

//监听
$ret = socket_listen($socket, 2);
if(!$ret) die("listen server fail:".socket_strerror(socket_last_error())."\n");
echo "waiting client...\n";

$clients = [$socket];
$recvs = [];

while(1){

    $read = $clients; //拷贝一份，socket_select会修改$read
    $ret = @socket_select($read, $write = NULL, $except = NULL,0);
    if($ret === false){
        break;
    }

    foreach ($read as $k=>$client) {

        //新连接
        if($client === $socket){
            //阻塞等待客户端连接
            $conn = socket_accept($socket);
            if(!$conn){
                echo "accept server fail:".socket_strerror(socket_last_error())."\n";
                break;
            }
            $clients[] = $conn;

            echo "client connect succ. fd: ".$conn."\n";

            //获取客户端IP地址
            socket_getpeername($conn, $addr, $port);
            echo "client addr: $addr:$port\n";

            //获取服务端IP地址
            socket_getsockname($conn, $addr, $port);
            echo "server addr: $addr:$port\n";

            // print_r($clients);
            echo "total: ".(count($clients)-1)." client\n";
        }else{
            //注意：后续使用$client而不是$conn
            if (!isset($recvs[$k]) ) $recvs[$k] = ''; //兼容可能没有值的情况

            $buffer = socket_read($client, 100); //每次读取100byte
            if($buffer === false || $buffer === ''){
                echo "client closed\n";
                unset($clients[array_search($client, $clients)]); //unset
                socket_close($client); //关闭本次连接
                break;
            }

            //解析单次消息，协议：换行符
            $pos = strpos($buffer, "\n");
            if($pos === false){ //消息未读取完毕，继续读取
                $recvs[$k] .= $buffer;
            }else{ //消息读取完毕
                $recvs[$k] .= trim(substr($buffer, 0, $pos+1)); //去除换行符及空格

                //客户端主动端口连接
                if($recvs[$k] == 'quit'){
                    echo "client closed\n";
                    unset($clients[array_search($client, $clients)]); //unset
                    socket_close($client); //关闭本次连接
                    break;
                }

                echo "recv:".$recvs[$k]."\n";
                socket_write($client, $recvs[$k]."\n"); //发送消息

                $recvs[$k] = '';
            }
        }
    }
}
socket_close($socket);