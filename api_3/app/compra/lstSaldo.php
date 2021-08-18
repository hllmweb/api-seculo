<?php
//lista de alunos com saldo almoço e crédito
//BD_PDV.SP_PORTAL_INFO

//pagamento 
//BD_PDV.SP_PORTAL_VENDA_PROD

// $p_cd_usuario_resp  = @$_POST['p_cd_usuario_resp'];
// $p_cd_usuario_alu   = null;

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);
// $p_operacao              = 'LA';
// $p_cd_usuario_resp       = @$_POST['p_cd_usuario_resp'];
// $p_cd_usuario_alu        = @$_POST['p_cd_usuario_alu'];
// $p_valor                 = null;
// $p_id_venda              = null;

// if(empty($p_cd_usuario_resp) || empty($p_cd_usuario_alu)){
//     echo json_encode(array('Erro' => 'Você não tem permissão de acesso!'));
//     exit();
// }


$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'operacao'        => 'LA',
    'cd_usuario_resp' => $params['p_cd_usuario_resp'],
    'cd_usuario_alu'  => $params['p_cd_usuario_alu'],
    'valor'           => $params['p_valor'],
    'id_venda'        => $params['p_id_venda']
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);
$conn = oci_connect(user, pass, tns, encode);

try{

    $sql    = 'BEGIN BD_PDV.SP_PORTAL_INFO(:P_OPERACAO, :P_CD_USUARIO_RESP, :P_CD_USUARIO_ALU, :P_VALOR, :P_ID_VENDA, :P_CURSOR); END;'; 
	$stid   = oci_parse($conn, $sql);
    $cursor = oci_new_cursor($conn);
    
    oci_bind_by_name($stid, ':P_OPERACAO',          $dados['operacao']); 
	oci_bind_by_name($stid, ':P_CD_USUARIO_RESP',   $dados['cd_usuario_resp']);
    oci_bind_by_name($stid, ':P_CD_USUARIO_ALU',    $dados['cd_usuario_alu']);
    oci_bind_by_name($stid, ':P_VALOR',             $dados['valor']);
    oci_bind_by_name($stid, ':P_ID_VENDA',          $dados['id_venda']);
	oci_bind_by_name($stid, ':P_CURSOR',            $cursor, -1, OCI_B_CURSOR);

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

?>