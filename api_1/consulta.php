
<?php

define('user', 'SISWEB');
define('pass', '17Sec001!xx');
define('tns', '10.228.20.18/bdiseculo.seculomanaus.com.br');///bdiseculo.seculomanaus.com.br
define('encode', 'AL32UTF8');


$p_id        = $_GET['id'];
$conn   = oci_connect(user, pass, tns, encode);

if (!$conn) {
    echo "Erro na Conexão com o Banco";
    $m = oci_error();
    trigger_error(htmlentities($m['message']), E_USER_ERROR);
}

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

// oci_free_statement($stid);
// oci_close($conn);

?>