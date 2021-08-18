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
	$sql = "SELECT R.RA, R.NM_ALUNO, M.ALERGIA, M.REMEDIO, M.MEDICO, M.SOCHOSP, M.TRATAMENTO, M.OUTRO,
    R.TURMA, R.NM_RESPONSAVEL, R.CPF_RESPONSAVEL 
    FROM RM.VW_ALUNO_RESP_RM_GERAL R 
    INNER JOIN RM.SALUNOFICHAMEDICA M ON M.RA = R.RA AND M.CODCOLIGADA = 1
    WHERE R.CPF_RESPONSAVEL = :CD_USUARIO";
	
	$stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_USUARIO',  $dados['cd_usuario']);

    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);

}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}


?>