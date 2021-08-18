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

	/*$sql = "SELECT P.CODIGO, A.RA, P.NOME AS NOME_ALUNO, TO_CHAR(C.DT_ACOMPANHAMENTO,'DD/MM/YYYY') AS DT_ACOMPANHAMENTO, AM.CODTURMA,
			C.COLACAO, C.ALMOCO, C.LANCHE, C.SONO, C.EVACUACAO, C.OBS_COLACAO, C.OBS_ALMOCO, C.OBS_LANCHE, C.OBS_SONO, C.OBS_EVACUACAO
			FROM RM.PPESSOA P
			LEFT JOIN RM.SALUNO A ON P.CODIGO = A.CODPESSOA
			LEFT JOIN RM.SMATRICPL AM ON A.RA = AM.RA
			LEFT JOIN RM.SHABILITACAOFILIAL H ON AM.IDHABILITACAOFILIAL = H.IDHABILITACAOFILIAL AND H.CODCURSO = '001'
			INNER JOIN RM.SPLETIVO SP ON SP.IDPERLET = AM.IDPERLET AND AM.CODCOLIGADA = SP.CODCOLIGADA AND SP.CODPERLET = TO_CHAR(SYSDATE,'YYYY')
			LEFT JOIN RM.STURMADISC STMD  ON H.IDHABILITACAOFILIAL = STMD.IDHABILITACAOFILIAL
			LEFT JOIN RM.SPROFESSORTURMA SPT ON STMD.IDTURMADISC = SPT.IDTURMADISC
			LEFT JOIN RM.ZMDACOMPAULA C ON C.RA = AM.RA AND  C.CODCOLIGADA = AM.CODCOLIGADA AND C.CODFILIAL = AM.CODFILIAL  
			AND TRUNC(C.DT_ACOMPANHAMENTO) = TO_DATE(SYSDATE,'DD/MM/YYYY')
			WHERE AM.CODSTATUS = 15  AND ROWNUM = 1 AND
			(  
				EXISTS(SELECT 1 FROM RM.VW_ALUNO_RESP_RM_PORTAL RESP
				WHERE RESP.CPF_RESPONSAVEL = :CD_USUARIO AND RESP.RA = AM.RA AND AM.CODTURMA = STMD.CODTURMA)
				OR
				EXISTS(SELECT 1 FROM RM.SCOORDENADOR SC
				JOIN RM.PPESSOA PP ON SC.CODPESSOA = PP.CODIGO WHERE PP.CODUSUARIO = :CD_USUARIO)
				OR
				EXISTS(SELECT 1 FROM RM.SPROFESSORTURMA PT
				JOIN RM.STURMADISC TD ON TD.IDTURMADISC = PT.IDTURMADISC AND PT.CODPROF= :CD_USUARIO)
			) ORDER BY P.NOME ASC";*/

	/*$sql = "SELECT X.CODPESSOA CODIGO,
			       X.RA,
			       X.NM_ALUNO NOME_ALUNO,
			       TO_DATE(Y.DT_ACOMPANHAMENTO, 'DD/MM/YYYY') DT_ACOMPANHAMENTO,
			       X.TURMA CODTURMA, 
			       Y.COLACAO COLACAO,
			       Y.LANCHE LANCHE,
			       Y.SONO SONO,
			       Y.EVACUACAO,
			       Y.OBS_COLACAO OBS_COLACAO,
			       Y.OBS_ALMOCO, 
			       Y.OBS_LANCHE, 
			       Y.OBS_SONO,
			       Y.OBS_EVACUACAO 
		FROM RM.VW_ALUNO_RESP_RM_GERAL X
		JOIN RM.ZMDACOMPAULA Y ON Y.RA = X.RA AND TRUNC(Y.DT_ACOMPANHAMENTO) BETWEEN TO_DATE(SYSDATE,'DD/MM/YYYY') AND TO_DATE(SYSDATE,'DD/MM/YYYY')
		WHERE X.RA = :CD_USUARIO OR X.CPF_RESPONSAVEL = :CD_USUARIO";*/

	$sql = "SELECT X.CODPESSOA AS CODIGO,
       X.RA,
       X.NM_ALUNO AS NOME_ALUNO,
       TO_CHAR(Y.DT_ACOMPANHAMENTO, 'DD/MM/YYYY') AS DT_ACOMPANHAMENTO,
       X.TURMA AS CODTURMA, 
	   case 
       when Y.COLACAO = 'AT' then 'Aceitação Total'
       when y.colacao = 'AP' then 'Aceitação Parcial'
       when y.colacao = 'RP' then 'Repetiu'
       when y.colacao = 'RJ' then 'Rejeição'
       when y.colacao = 'AS' then 'Ausente'
       end AS COLACAO,
       case 
       when y.almoco = 'AT' then 'Aceitação Total'
       when y.almoco = 'AP' then 'Aceitação Parcial'
       when y.almoco = 'RP' then 'Repetiu'
       when y.almoco = 'RJ' then 'Rejeição'
       when y.almoco = 'AS' then 'Ausente'
       end as almoco,
       case 
       when Y.LANCHE = 'AT' then 'Aceitação Total'
       when y.lanche = 'AP' then 'Aceitação Parcial'
       when y.lanche = 'RP' then 'Repetiu'
       when y.lanche = 'RJ' then 'Rejeição'
       when y.lanche = 'AS' then 'Ausente'
       end AS LANCHE,
       case 
       when Y.SONO = 'TQ' then 'Tranquilo'
       when y.sono = 'AG' then 'Agitado'
       when y.sono = 'ND' then 'Não Dormiu'
       when y.sono = 'AS' then 'Ausente'
       end AS SONO,
       case 
       when Y.EVACUACAO = 'NO' then 'Normal'
       when y.evacuacao = 'NE' then 'Não Evacuação'
       when y.evacuacao = 'AS' then 'Ausente'
       end AS EVACUACAO,     
       Y.OBS_COLACAO AS OBS_COLACAO,
       Y.OBS_ALMOCO, 
       Y.OBS_LANCHE, 
       Y.OBS_SONO,
       Y.OBS_EVACUACAO 
FROM RM.VW_ALUNO_RESP_RM_GERAL X
JOIN RM.ZMDACOMPAULA Y ON Y.RA = X.RA AND TRUNC(Y.DT_ACOMPANHAMENTO) BETWEEN TO_CHAR(SYSDATE,'DD/MM/YYYY') AND TO_CHAR(SYSDATE,'DD/MM/YYYY')
WHERE X.RA = :CD_USUARIO OR X.CPF_RESPONSAVEL = :CD_USUARIO";

	$stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_USUARIO',  $dados['cd_usuario']);

    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);


}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}



?>