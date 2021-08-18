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

    $sql    = 'SELECT PI.CD_ALUNO, AP.CD_PROVA, AP.NUM_PROVA, AP.CHAMADA, AP.PERIODO, AP.DT_PROVA, AP.TITULO, C.NM_CURSO, AP.BIMESTRE FROM BD_SICA.AVAL_PROVA AP  
    		   INNER JOIN BD_SICA.AVAL_PROVA_INSCRITOS PI ON PI.CD_PROVA = AP.CD_PROVA 
			   INNER JOIN BD_SICA.CURSOS C ON C.CD_CURSO = AP.CD_CURSO WHERE PI.CD_ALUNO = :CD_ALUNO AND AP.PERIODO = (SELECT CL_PERIODO_ATUAL FROM BD_SICA.CONFIGURACAO) ORDER BY AP.BIMESTRE ASC';

    $stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_ALUNO', $dados['cd_usuario']);
    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);

}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}


?>