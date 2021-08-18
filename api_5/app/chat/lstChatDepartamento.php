<?php


header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
	'opcao' => $params['p_opcao']
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados['opcao']);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);

try{

	$sql = "SELECT * FROM BD_APLICACAO.LOGIN_APP WHERE USU_LOGIN IN('00000000001','00000000002','00000000003')";
	$stid   = oci_parse($conn, $sql); 

    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);
}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}

?>