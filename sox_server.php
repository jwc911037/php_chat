<?php

	$host = "localhost";
	$port = 8888;

	$i = 0;//server的聊天訊息計數器，就是現在全部大家聊到第幾句了的統合計數器
	$code="^znl^"; //自訂區隔碼

	//設定沒有時間限制，不會time out
	set_time_limit(0);

	// 建立 socket
	$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("無法建立socket");

	// 綁定 socket
	$result = socket_bind($socket, $host, $port) or die("沒有辦法綁定socket");

	// 說明:這就是Never Ending Story，永遠運作中...

	while(ture){

		// start listening for connections 第一個是監聽通道
		$result = socket_listen($socket, 3) or die("無法設定socket監聽器");

		// accept incoming connections
		// spawn another socket to handle communication 另一個通道來做通訊處理
		$spawn = socket_accept($socket) or die("Could not accept incoming connection");

		// read client input 獲取client資訊
		$input = socket_read($spawn, 1024) or die("無法讀取input");

		// clean up input string
		$input = trim($input);

		$len=strlen($input);

		$pos1=strpos($input,$code); //和sox_chat.php之定義相同，以下皆同

		$pos2=$pos1+5;

		$num_str=substr($input,0,$pos1);

		$num = (int)$num_str;

		if($num==0) $num=$i;//如果num為0，表示該用戶第一次進聊天室，因此把該用戶的聊天訊息計數器值設為server的聊天訊息計數器目前的值

		$msg=substr($input,$pos2,$len);

		//如果來訊是輪詢者代號，將訊息改為空。
		if($msg == "!!!null!!!" ){
			$msg="" ;
		}else{ // 若非輪詢者，那就是聊天訊息

			$i=$i+1; //將聊天訊息計數器加1
			$mg[$i]="有人說: ".$msg . ".........over! ";// 聊天訊息儲存到陣列中

		}

		//統計累積該用戶的計數器(num)到伺服器的計數器($i)的所有新聊天訊息，接下來要送回給用戶client顯示的
		for($c=$num+1; $c<$i+1; $c++){
			$output= $output . $mg[$c] ;
		}

		$send_num= sprintf( "%d",$i ); //把數字轉變成字串
		$output=$send_num.$code.$output;//數字加在檔頭送回給client，表示現在用戶看到第幾句話了，就是用戶的num

		//將目前msg計數器+區隔碼+所有新msg訊息 傳回給client
		socket_write($spawn, $output, strlen ($output)) or die("無法寫出 output");

		$output="";

	}

	// close sockets 既然永遠在連線工作，也不用關閉了
	//socket_close($spawn);
	//socket_close($socket);

?>