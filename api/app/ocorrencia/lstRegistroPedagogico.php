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
	$sql = "SELECT P.RA, P.NM_ALUNO, P.TURMA, L.USU_LOGIN AS CPF_RESPONSAVEL, P.NM_RESPONSAVEL, 
			SUBSTR(RM.F_OCORRENCIA_ALU(O.CODCOLIGADA, O.IDOCORALUNO),1,255) AS OBS
			FROM RM.VW_ALUNO_RESP_RM_GERAL P
			 INNER JOIN BD_APLICACAO.LOGIN_APP L ON L.USU_LOGIN = P.CPF_RESPONSAVEL AND L.FLG_ATIVO = 'S' AND L.USU_TIPO = 'R'
			 INNER JOIN RM.SOCORRENCIAALUNO O ON O.RA = P.RA AND NVL(O.DISPONIVELWEB,0) = 1
			 INNER JOIN RM.SOCORRENCIATIPO OT ON OT.CODOCORRENCIAGRUPO = O.CODOCORRENCIAGRUPO AND OT.CODCOLIGADA = O.CODCOLIGADA
			WHERE NVL(OT.DISPONIVELWEB,0) = 1
			AND L.USU_LOGIN = :CD_USUARIO AND P.CODPERLET = TO_CHAR(SYSDATE,'YYYY')
			ORDER BY O.RECCREATEDBY DESC";

	$stid = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_USUARIO',  $dados['cd_usuario']);

    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);
		

}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}

?>