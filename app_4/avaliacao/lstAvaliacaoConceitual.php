<?php 
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'cd_usuario' => $params['p_cd_usuario'],
    'bimestre'	 => $params['p_bimestre']
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);

try{
	$sql = "SELECT 
    QC.ID_QUESTCAT, 
    ZQA.CODTURMA,
    ZQA.CODPERLET AS PERIODO,
    CASE 
    WHEN ZQA.CODETAPA = 16 THEN '1º BIMESTRE'
    WHEN ZQA.CODETAPA = 26 THEN '2º BIMESTRE'
    WHEN ZQA.CODETAPA = 36 THEN '3º BIMESTRE'
    WHEN ZQA.CODETAPA = 46 THEN '4º BIMESTRE' END AS BIMESTRE, 
    ZQA.RA,
    QC.NOME, 
    QP.DESCRICAO,
    (
        SELECT QA.RESPOSTA FROM RM.ZMDQUESTAULA QA WHERE QA.IDQUESTPER = QP.IDQUESTPER
    ) AS RESPOSTA
	FROM RM.ZMDQUESTPER QP
	INNER JOIN RM.ZMDQUESTCAT QC ON QC.ID_QUESTCAT = QP.IDQUESTCAT
	INNER JOIN RM.ZMDQUESTAULA ZQA ON ZQA.IDQUESTCAT = QC.ID_QUESTCAT   
	WHERE (
	    EXISTS(SELECT 1 FROM RM.VW_ALUNO_RESP_RM_PORTAL RESP WHERE (RESP.RA = :CD_USUARIO OR RESP.CPF_RESPONSAVEL = :CD_USUARIO) AND ROWNUM <= 1) --RESP.RA = QA.RA AND
	) AND ZQA.CODETAPA = :BIMESTRE";


	$stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_USUARIO',  $dados['cd_usuario']);
    oci_bind_by_name($stid, ':BIMESTRE',  	$dados['bimestre']);

    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);


}catch(Exception $e){
	echo 'Erro: '.$e->getMessage();
}

?>