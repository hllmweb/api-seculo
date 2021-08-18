<?php

require_once('../config/conection.php');

$p_id  			= null;
$p_encodes   	= @$_POST['encodes'];
$p_operacao     = 2;  //valid encoding

if(empty($p_encodes))
{
	echo json_encode(array('Erro'=>'Você não tem permissão de acesso!'));
	exit();
}

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'encodes' 	=> $p_encodes
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



// $result = mysqli_query($conn, $sql);

// while($row = mysqli_fetch_assoc($result))
// {
//     $arr_result[] = $row;

// }
// if(empty($nome)){
//     echo json_encode(array('Erro'=>'Você não tem permissão de acesso'));
// }else{
//     echo json_encode($arr_result);
// }

curl_close($iniciar);
?>