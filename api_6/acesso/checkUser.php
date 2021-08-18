<?php

require_once('../config/conection.php');

$p_id   		= @$_POST['id'];
$p_encodes   	= null;
$p_operacao     = 3;


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
	
	$sql    = 'BEGIN BD_APLICACAO.SP_ENCODING(:P_OPERACAO, :P_ID, :P_ENCODES, :RC1); END;'; 
	$stid   = oci_parse($conn, $sql);
	$cursor = oci_new_cursor($conn);

	oci_bind_by_name($stid, ':P_OPERACAO', 	$p_operacao); 
	oci_bind_by_name($stid, ':P_ID',       $p_id);
	oci_bind_by_name($stid, ':P_ENCODES',   $p_encodes);
	oci_bind_by_name($stid, ':RC1', $cursor, -1, OCI_B_CURSOR);

	oci_execute($stid);
	oci_execute($cursor, OCI_DEFAULT);  

	oci_fetch_all($cursor, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);


    while (($row = oci_fetch_array($cursor, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
    	$result['RETORNO'] = $row;
	}

	echo json_encode($result);	

}catch(Exception $e){
	echo 'Erro: '.$e->getMessage();
}



// oci_free_statement($stid);
// oci_close($conn);

?>