<?php

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');
// require __DIR__.'/socket/vendor/autoload.php';

// $loop = React\EventLoop\Factory::create();
// $connector = new Ratchet\Client\Connector($loop);

$params = json_decode(file_get_contents('php://input'), TRUE);


$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'origem'   	   => $params['p_origem'],
    'destino'	   => $params['p_destino']
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);


/*$result = array(
	'departamento' => $dados['departamento'],
	'origem'	   => $dados['origem'],
	'destino' 	   => $dados['destino'],
	'mensagem'	   => $dados['mensagem']
);*/

try{
	
	$sql = "SELECT TO_CHAR(C.DATAHORA,'DD/MM/YYYY HH24:MI:SS') AS DATAHORA, C.USU_LOGIN, 
				  C.USU_LOGIN_DESTINO, L.NOME, C.MENSAGEM, C.DATAHORA_LIDA, C.FLG_DELETADO 
        FROM BD_APLICACAO.LOGIN_APP L
        INNER JOIN BD_APLICACAO.CHAT C ON C.USU_LOGIN = L.USU_LOGIN AND C.FLG_DELETADO IS NULL
        WHERE ((C.USU_LOGIN = :CD_ORIGEM AND C.USU_LOGIN_DESTINO = :CD_DESTINO) OR
              (C.USU_LOGIN_DESTINO = :CD_ORIGEM AND C.USU_LOGIN = :CD_DESTINO)) ORDER BY C.DATAHORA ASC";

 	
 	$stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_ORIGEM',    $dados['origem']);
    oci_bind_by_name($stid, ':CD_DESTINO', 	 $dados['destino']);

    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);


 // echo json_encode($result);
 // $msg = 'teste';
 //    \Ratchet\Client\connect('wss://seculomanaus.com.br/wss2/:3000/api/app/chat/socket')->then(function($conn) {
 //        $conn->on('message', function($msg) use ($conn) {
 //            echo json_encode("Received: {$msg}\n");
 //            $conn->close();
 //        });

 //        $conn->send('Hello World!');
 //    }, function ($e) {
 //        echo json_encode("Could not connect: {$e->getMessage()}\n");
 //    });

}catch(Exception $e){
    echo json_encode('Erro: '.$e->getMessage());
}

?>