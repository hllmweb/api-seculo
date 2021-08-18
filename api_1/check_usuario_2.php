<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

define('user', 'SISWEB');
define('pass', '17Sec001!xx');
define('tns', '10.228.20.18/bdiseculo.seculomanaus.com.br');///bdiseculo.seculomanaus.com.br
define('encode', 'AL32UTF8');


$p_id   = @$_POST['id'];


$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'id' 		=> $p_id
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);

$conn   = oci_connect(user, pass, tns, encode);

try{
	$sql    = 'SELECT * FROM BD_CONTROLE.USUARIOS WHERE CD_USUARIO = :CD_USUARIO'; 
	$stid   = oci_parse($conn, $sql);

	oci_bind_by_name($stid, ':CD_USUARIO',$p_id);
	oci_execute($stid);


	$result['RETORNO'] = array();
	while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
		if($row['CD_USUARIO'] != $p_id){
			$result['RETORNO'] = "N";
		}else{
			$result['RETORNO'] = "S";
		}
			
	}	
	echo "[".json_encode($result)."]";

}catch(Exception $e){
	echo 'Erro: '.$e->getMessage();
}



// oci_free_statement($stid);
// oci_close($conn);

?>