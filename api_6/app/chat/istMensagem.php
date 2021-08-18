<?php

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'origem'   	   => $params['p_origem'],
    'destino'	   => $params['p_destino'],
    'mensagem'	   => $params['p_mensagem']
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);

try{
	$sql = "INSERT INTO BD_APLICACAO.CHAT (USU_LOGIN, USU_LOGIN_DESTINO, MENSAGEM) VALUES (:CD_ORIGEM, :CD_DESTINO, :MENSAGEM)";
	$stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_ORIGEM',    $dados['origem']);
    oci_bind_by_name($stid, ':CD_DESTINO', 	 $dados['destino']);
    oci_bind_by_name($stid, ':MENSAGEM',  	 $dados['mensagem']);

    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);

}catch(Exception $e){
    echo json_encode('Erro: '.$e->getMessage());
}

?>