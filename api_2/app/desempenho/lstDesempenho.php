<?php

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'cd_usuario'  => $params['p_cd_usuario'],
    'turmadisc'   => $params['p_cd_turmadisc'],
    'turma'       => $params['p_cd_turma'],
    'coddisc'     => $params['p_cd_coddisc'],
    'etapa'       => $params['p_cd_etapa'],
);


curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);

try{

    $sql    = 'BEGIN RM.SP_ALUNO_DESEMPENHO(:P_CODCOLIGADA, :P_IDPERLET, :P_RA, :P_IDTURMADISC, :P_CODTURMA, :P_CODDISC, :P_CODETAPA, :P_CURSOR); END;';
    $stid   = oci_parse($conn, $sql); 
    $cursor = oci_new_cursor($conn);

    oci_bind_by_name($stid, ':P_CODCOLIGADA', 1);
    oci_bind_by_name($stid, ':P_IDPERLET'   , 1);
    oci_bind_by_name($stid, ':P_RA'         , $dados['cd_usuario']);
    oci_bind_by_name($stid, ':P_IDTURMADISC', $dados['turmadisc']);
    oci_bind_by_name($stid, ':P_CODTURMA'   , $dados['turma']);
    oci_bind_by_name($stid, ':P_CODDISC'    , $dados['coddisc']);
    oci_bind_by_name($stid, ':P_CODETAPA'   , $dados['etapa']);
    oci_bind_by_name($stid, ':P_CURSOR'     , $cursor, -1, OCI_B_CURSOR);
    oci_execute($stid);
    oci_execute($cursor, OCI_DEFAULT);
    oci_fetch_all($cursor, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);

}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}

?>