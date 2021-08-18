<?php

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'cd_usuario'  => $params['p_cd_usuario']
);


curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);

try{

    $sql    = "SELECT 
              RM.F_COEFICIENTE_REND(P_CODCOLIGADA=>1, P_IDPERLET=>2, P_RA=>:CD_ALUNO, P_CODETAPA=>16) MB1,
              RM.F_COEFICIENTE_REND(P_CODCOLIGADA=>1, P_IDPERLET=>2, P_RA=>:CD_ALUNO, P_CODETAPA=>26) MB2,
              RM.F_COEFICIENTE_REND(P_CODCOLIGADA=>1, P_IDPERLET=>2, P_RA=>:CD_ALUNO, P_CODETAPA=>36) MB3,
              RM.F_COEFICIENTE_REND(P_CODCOLIGADA=>1, P_IDPERLET=>2, P_RA=>:CD_ALUNO, P_CODETAPA=>46) MB4
             FROM DUAL";

   
    $stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_ALUNO',    $dados['cd_usuario']);

    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);

}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}

?>