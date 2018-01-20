<?php
	//接收來自sox_client.php的資訊

	$msg=$_GET["msg"];

	//準備socket工作了
	$host = "localhost";
	$port = 8888;

	// 創建 socket
	$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

	// 連結到server
	$con = socket_connect($socket, $host, $port) or die("這個聊天室server根本沒打開吧?耍寶
	啊!");

	//之前debug確認用:if($con) echo "太帥了吧！成功建立連線了!!" ;

	// 送出資訊給 server
	socket_write($socket, $msg, strlen($msg)) or die("Could not send data to server\n");

	// 然後得到 server 回返之資訊
	$result = socket_read ($socket, 1024) or die("Could not read server response\n");

	//將此資訊回傳給sox_chat.php
	echo $result;

	// 關閉 socket
	socket_close($socket);

?>