<!DOCTYPE html>
<html>
<head>

	<title>ORZtobias之AJAX+SOCKET聊天室</title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<style>
		html,body{font:normal 0.9em arial,helvetica;}
		#log {width:300px; height:600px; border:10px solid #7F9DB9; overflow:auto;}
		#msg {width:200px;border:3px solid #555555;}
	</style>

<script>

	var num=0; //num定義為chat和server互相確認之目前第幾句msg
	var http_request =false;//看到這就知道AJAX在準備了

	function send(){
		var txt,msg;
		txt = $("msg");
		msg = txt.value;
		if(!msg){ 
			alert("訊息不能為全空白的啦"); return; 
		}

		str_num =num.toString()

		//要傳出去給server的數據為複合資訊: 聊天訊息計數+區隔碼+輸入之訊息
		msg=str_num+"^znl^"+msg; 
		txt.value="";
		//txt.focus();

		try {
			http_request = new XMLHttpRequest();
		}
		catch (failed){
			http_request = false;
		}

		if (!http_request){
			alert("Error initializing XMLHttpRequest!");
		}

		var url = "sox_ajax.php?msg=" + msg;
		http_request.open("GET", url, true);
		http_request.send(null);
		http_request.onreadystatechange = function() { alertContents(http_request); };

	}

	//====此段為接收server回傳值並進行處理後，再丟上螢幕顯示====

	function alertContents(http_request) {
	if (http_request.readyState == 4) {
		if (http_request.status == 200) {

			var doc = http_request.responseText;//從server傳回來的資訊

			//將server傳回來之資訊:"聊天訊息序號"+區隔碼+"聊天訊息"，拆開來。
			var pos1;
			var pos2;
			var sv_msg;
			var leng;
			var str_num;

			leng=doc.length;
			pos1=doc.indexOf("^znl^"); //位置1為聊天訊息序號的最後一碼位置
			pos2=pos1+5;//位置2為聊天訊息的起始位置
			str_num=doc.substring(0,pos1);//得到聊天訊息序號，不過這是字串
			//這個真number再來是要傳給server的，表示最後讀取自server之訊息序號
			num = parseInt(str_num) ;
			sv_msg=doc.substring(pos2,leng);//這就是server傳來的目前最後一則聊天訊息

			//以下，pos2=leng，表示只傳回來這兩個:num^znl^，識別碼後面沒有東西了，不用做任何動作
			//else則秀出聊天訊息

			if (pos2==leng){

			}else{
				log_show(sv_msg);
			}
		}

	}

	}
	setInterval("polling();", 5000) //每隔五秒發出一次查詢

	function polling(){
		try {
			http_request = new XMLHttpRequest();
		}
		catch (failed){
			http_request = false;
		}

		if (!http_request){
			alert("Error initializing XMLHttpRequest!");
		}

		//此乃輪詢者代號，嘿嘿!server收到這個就知道這不是聊天訊息，而是神秘輪詢者

		var polling="!!!null!!!";

		str_num =num.toString();//將數字轉成字串

		polling=str_num+"^znl^"+polling;//組合成複合資訊，傳出給server詢問用

		var url = "sox_ajax.php?msg=" + polling;

		http_request.open("GET", url, true);
		http_request.send(null);
		http_request.onreadystatechange = function() { alertContents(http_request); };

	}

	function quit(){
		log_show("沒事幹嘛Quit ?? Quit是無力的，直接把網頁關掉即可... ");
	}

	// Utilities 輸入介面相關設定
	function $(id){ return document.getElementById(id); }
	function log_show(msg){ $("log").innerHTML+="<br>"+msg; }
	function onkey(event){ if(event.keyCode==13){ send(); } } //這是enter 功能
</script>

</head>
<body>
	<h3>SOCKET + AJAX 【五秒一次】Seven Style聊天室</h3>
	<div id="log"></div>
	<input id="msg" type="textbox" onkeypress="onkey(event)"/>
	<button onclick="send()">Send</button>
	<button onclick="quit()">Quit</button>
	<div>ok,你可以開始打字了</div>
</body>
</html>