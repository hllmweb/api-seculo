<?php 
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'cd_usuario' => $params['p_cd_usuario']
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);

try{

    $sql    = "SELECT H.ID_VENDA, U.CD_USUARIO, U.NM_USUARIO, TO_CHAR(H.DT_MOVIMENTO,'DD/MM/YYYY HH24:MI') AS DT_MOVIMENTO,
	H.OBSERVACAO, H.SALDO_ATUALIZADO, H.SALDO_RESULTADO
	FROM BD_CONTROLE.HIST_SALDO_CREDITO H
	INNER JOIN BD_CONTROLE.USUARIOS U ON U.CD_USUARIO=H.CD_USUARIO
	WHERE
	(
		EXISTS(SELECT 1 FROM RM.VW_ALUNO_RESP_RM_GERAL RESP WHERE RESP.RA = H.CD_USUARIO AND (RESP.RA = :CD_USUARIO OR RESP.CPF_RESPONSAVEL = :CD_USUARIO) AND ROWNUM <= 1)
	)  
	AND TO_CHAR(H.DT_MOVIMENTO,'YYYY') > '2019' ORDER BY H.DT_MOVIMENTO DESC"; 
  	$stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_USUARIO',  $dados['cd_usuario']);

    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);

}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}

?>