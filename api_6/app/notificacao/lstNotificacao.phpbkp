<?php 
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'cd_usuario' 		=> $params['p_cd_usuario']
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);


try{

	$sql = "SELECT N.ID_NOTIFICACAO,
        N.TITULO,
        N.MENSAGEM,
        N.DT_NOTIFICAR,
        N.HR_NOTIFICAR,
        AL.USU_LOGIN,
        AL.RA,
        AL.NM_ALUNO,
        AL.CELULAR,
        AL.NM_RESPONSAVEL
  FROM
  BD_APLICACAO.APP_NOTIFICACAO N
  INNER JOIN (SELECT  L.USU_LOGIN, A.RA, A.NM_ALUNO, A.CODCURSO, A.TURMA AS CODTURMA, 'A' ID_PUB, A.ALU_TEL_CEL AS CELULAR, A.NM_RESPONSAVEL  FROM RM.VW_ALUNO_RESP_RM_GERAL A
              INNER JOIN BD_APLICACAO.LOGIN_APP L ON L.USU_LOGIN = A.RA AND L.USU_LOGIN = :CD_USUARIO
              UNION
              SELECT  L.USU_LOGIN, A.RA, A.NM_ALUNO, A.CODCURSO, A.TURMA AS CODTURMA, 'R' ID_PUB, A.CEL_RESPONSAVEL AS CELULAR, A.NM_RESPONSAVEL FROM RM.VW_ALUNO_RESP_RM_GERAL A
              INNER JOIN BD_APLICACAO.LOGIN_APP L ON L.USU_LOGIN = A.CPF_RESPONSAVEL AND L.USU_LOGIN = :CD_USUARIO WHERE A.CPF_RESPONSAVEL IS NOT NULL
              ) AL ON (N.ID_PUB_DESTINO = 'T' OR N.ID_PUB_DESTINO = AL.ID_PUB) 
              AND
              (
                (N.CODCURSO_DESTINO IS NULL OR N.CODCURSO_DESTINO = AL.CODCURSO) AND 
                (N.CODTURMA_DESTINO IS NULL OR N.CODTURMA_DESTINO = AL.CODTURMA)
              )
 WHERE
 N.FLG_NOTIFICAR = 'S'
 AND 
 (
     (1=1 AND  N.ID_SERVICO IN (1,3)  )
     OR
     (1=4 AND  N.ID_SERVICO=2)
 )
  AND N.ID_PUB_DESTINO IN ('A','R','T')
  AND NOT EXISTS(
      SELECT 1 FROM BD_APLICACAO.APP_NOTIFICACAO_ENVIADA E WHERE E.USU_LOGIN = AL.USU_LOGIN AND 
                E.ID_NOTIFICACAO = N.ID_NOTIFICACAO)";
    $stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_USUARIO',    $dados['cd_usuario']);

    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);

	//echo json_encode($result);
}catch(Exception $e){
	echo 'Erro: '.$e->getMessage();
}

?>