<!DOCTYPE html>
<html>
<head>
	<title></title>

</head>
<body>

	<script>
		//WebSocket
	    var conn = new WebSocket('wss://seculomanaus.com.br/wss2/:8282/api/app/chat/socket');
	    
	    conn.onopen = function(e) {
	        console.log("ConexÃ£o Estabelecida!");
	    };
	</script>
</body>
</html>