<?php

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);


$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'cd_usuario' 		=> $params['p_cd_usuario'],
    'usu_senha'         => $params['p_usu_senha']
);


curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_setopt($iniciar, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: '.strlen($dados))                                                                       
);   
curl_exec($iniciar);

$conn   = oci_connect(user, pass, tns, encode);

try{

    $sql    = 'SELECT * FROM BD_SICA.VW_LOGIN_APP WHERE USU_LOGIN = :CD_USUARIO AND USU_SENHA = :USU_SENHA'; 
	$stid   = oci_parse($conn, $sql);

    oci_bind_by_name($stid, ':CD_USUARIO', $dados['cd_usuario']);
    oci_bind_by_name($stid, ':USU_SENHA',  $dados['usu_senha']);
    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

    echo json_encode($result);


}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}

?>