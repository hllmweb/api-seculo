<?php 
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'cd_usuario'        => $params['p_cd_usuario']
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
                   P.USU_LOGIN,

                    CASE
                     WHEN APP.USU_TIPO = 'A' THEN
                    (
                     SELECT  DISTINCT A.ALU_TEL_CEL AS CELULAR  FROM RM.VW_ALUNO_RESP_RM_GERAL A
                              INNER JOIN BD_APLICACAO.LOGIN_APP L ON L.USU_LOGIN = A.RA
                              WHERE  L.USU_LOGIN =  P.USU_LOGIN
                     )
                     ELSE
                     (
                     SELECT  DISTINCT A.CEL_RESPONSAVEL AS CELULAR FROM RM.VW_ALUNO_RESP_RM_GERAL A
                              INNER JOIN BD_APLICACAO.LOGIN_APP L ON L.USU_LOGIN = A.CPF_RESPONSAVEL
                                                                    WHERE A.CPF_RESPONSAVEL IS NOT NULL
                                                                    AND L.USU_LOGIN =  P.USU_LOGIN
                      )
                    END AS CELULAR

             FROM
             BD_APLICACAO.APP_NOTIFICACAO N
             INNER JOIN  BD_APLICACAO.APP_NOTIFICACAO_PESSOAS P ON P.ID_NOTIFICACAO = N.ID_NOTIFICACAO
             INNER JOIN  BD_APLICACAO.LOGIN_APP APP ON APP.USU_LOGIN = P.USU_LOGIN
             WHERE
             N.FLG_NOTIFICAR = 'S' AND P.USU_LOGIN =  :CD_USUARIO
             AND N.ID_PUB_DESTINO IN ('E')
             AND (
              TRUNC(N.DT_NOTIFICAR)=TRUNC(SYSDATE) AND TO_CHAR(SYSDATE,'HH24:MI')>=N.HR_NOTIFICAR  --SE DATA ATUAL FOR IGUAL A DATA E HORA AGENDADA
              OR
              TRUNC(SYSDATE) - TRUNC(N.DT_NOTIFICAR) BETWEEN 1 AND 7 --OU TODAS AS NOTIFICACOES DOS ULTIMOS 7 DIAS
             )";
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