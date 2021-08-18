<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);

// $p_cd_usuario = @$_POST['p_cd_usuario'];


// if(empty($p_cd_usuario)){
//     echo json_encode(array('Erro' => 'Você não tem permissão de acesso!'));
//     exit();
// }

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'cd_usuario' => $params['p_cd_usuario']
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados['cd_usuario']);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);

try{
	//SELECT * FROM RM.VW_ALUNO_NOTAS_FALTA_II  WHERE RA = '14003082' AND CODPERLET = TO_CHAR(SYSDATE,'YYYY') ORDER BY NM_DISCIPLINA_RED 
    $sql = 'SELECT * FROM RM.VW_ALUNO_NOTAS_FALTA_II  WHERE CODPERLET = 2021 AND RA = :P_CD_USUARIO ORDER BY NM_DISCIPLINA_RED';
	$stid   = oci_parse($conn, $sql);

    oci_bind_by_name($stid, ':P_CD_USUARIO', $dados['cd_usuario']);
    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

    echo json_encode($result);


}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}



?>