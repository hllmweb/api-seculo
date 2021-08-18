<?php 
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../config/conection.php');

$p_cd_usuario = @$_POST['p_cd_usuario'];
$p_usu_senha  = @$_POST['p_usu_senha'];


/*
 *  autenticação de acesso 
 */


if(empty($p_cd_usuario) || empty($p_usu_senha)){
    echo json_encode(array('Erro'=>'Você não tem permissão de acesso!'));
    exit();
}

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = json_encode(array(
    'cd_usuario' 		=> $p_cd_usuario,
    'usu_senha'         => $p_usu_senha
));

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

    oci_bind_by_name($stid, ':CD_USUARIO', $p_cd_usuario);
    oci_bind_by_name($stid, ':USU_SENHA',  $p_usu_senha);
    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

    echo json_encode($result);


}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}


?>