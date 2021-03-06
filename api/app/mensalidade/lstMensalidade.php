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
	$sql = "SELECT  
    A.CODIGO,
    B.CPF_RESPONSAVEL,
    B.CODIGO_BARRA,
    A.NOME AS NM_ALUNO, 
    B.CD_ALUNO, 
    B.MES_REFERENCIA, 
    B.NM_PRODUTO, 
    B.DATAVENCIMENTO AS DT_VENCIMENTO,
 CASE 
     WHEN TRUNC(B.DATAVENCIMENTO) > TRUNC(SYSDATE) THEN 
     'N' 
     ELSE 
     'S' 
 END AS FLG_VENCIDO,
 B.VALOR_LIQUIDO AS VALOR_BOLETO,
 B.VALORBAIXADO AS VALOR_RECEBIDO,
 B.DATABAIXA AS DT_BAIXA,
 0 AS CD_BOLETO,
 '          ' AS NF_NUMERO,
 '          ' AS NF_CODIGO_VERIFICACAO,
 'N' AS TITULO_PROTESTADO,
 0 AS FLG_JURIDICAO
 FROM (
     SELECT(
         SELECT B.CODIGOBARRA FROM RM.FBOLETO B
         JOIN  RM.FLANBOLETO LB ON B.CODCOLIGADA=B.CODCOLIGADA AND LB.IDBOLETO = B.IDBOLETO
         WHERE B.STATUS = 0 AND LB.IDLAN = L.IDLAN --0 AND LB.IDLAN = L.IDLAN
     ) AS CODIGO_BARRA, 
     REGEXP_REPLACE(C.CGCCFO, '[^[:digit:]]') AS CPF_RESPONSAVEL,
     RPAD(' ',18) AS NOSSO_NUMERO,
     TRIM(REPLACE(LPAD('x',9, REGEXP_REPLACE(' ' || L.HISTORICO, '[^[:digit:]]')),'x','')) AS CD_ALUNO,
     TO_CHAR (L.DATAVENCIMENTO, 'MM/YYYY') AS MES_REFERENCIA ,
     L.HISTORICO,
     D.DESCRICAO AS NM_PRODUTO, 
     C.NOMEFANTASIA, 
     C.TELEFONE AS TELEFONE_RESPONSAVEL,
     TO_CHAR (L.DATAEMISSAO, 'DD/MM/YYYY') AS DATA_EMISSAO, 
     L.VALORORIGINAL,
     L.VALORDESCONTO, 
     BOLSA.VALOR AS VALOR_BOLSA, 
     DESCONTO.VALOR AS VALORDESCONTO_EDU,
     L.VALORMULTA,
   CASE 
        WHEN L.DATAVENCIMENTO < TRUNC(SYSDATE) THEN 
            (L.VALORORIGINAL - L.VALORDESCONTO) * ( TRUNC(SYSDATE - L.DATAVENCIMENTO) * 0.0333) /100
        ELSE 0
    END AS VALOR_JUROS, 
    CASE L.STATUSLAN WHEN 0 THEN 'ABERTO' WHEN 1 THEN 'BAIXADO' WHEN 2 THEN 'CANCELADO' END AS STATUS_LAN, 
    FP.DESCFORMAPAGTO AS FORMA_PGTO, L.CODTDO AS TIPO_DOCUMENTO, L.DATAEMISSAO, L.DATAVENCIMENTO, L.CODAPLICACAO,
    CASE WHEN L.DATAVENCIMENTO < TRUNC(SYSDATE)  AND  L.DATABAIXA IS NULL
        THEN 
        (L.VALORORIGINAL - NVL (BOLSA.VALOR,0)
        +(L.VALORORIGINAL - NVL (BOLSA.VALOR,0)) * (TRUNC(SYSDATE - L.DATAVENCIMENTO) * 0.0333) /100
        + L.VALORMULTA)
        ELSE  
        L.VALORORIGINAL - L.VALORDESCONTO - NVL (BOLSA.VALOR,0) - NVL (DESCONTO.VALOR,0)
        END   
     AS VALOR_LIQUIDO,  
       L.VALORBAIXADO,
       L.DATABAIXA  
    FROM RM.FLAN L
        left JOIN RM.FCFO C ON C.CODCFO = L.CODCFO
        left JOIN RM.FLANBAIXA LB ON LB.IDLAN = L.IDLAN AND STATUS <> 1
        left JOIN RM.TFORMAPAGTO FP ON FP.IDFORMAPAGTO = LB.IDFORMAPAGTO
        left JOIN RM.FTDO D ON D.CODTDO = L.CODTDO
        left JOIN RM.FLANINTEGRACAO BOLSA ON BOLSA.IDLAN = L.IDLAN AND BOLSA.IDCAMPO = 43
        left JOIN RM.FLANINTEGRACAO DESCONTO ON DESCONTO.IDLAN = L.IDLAN AND DESCONTO.IDCAMPO = 44
    WHERE L.PAGREC=1 
    AND L.CODTDO IN('MENSAL','MAT') 
    AND TRUNC(L.DATAVENCIMENTO) > SYSDATE
    AND EXISTS(
            SELECT 1 FROM RM.VW_ALUNO_RESP_RM_PORTAL AR WHERE (AR.CPF_RESPONSAVEL = :CD_USUARIO OR AR.RA = :CD_USUARIO) 
            AND ROWNUM <= 1
            AND AR.RA = TRIM(REPLACE(LPAD('x',9, REGEXP_REPLACE(' ' || L.HISTORICO, '[^[:digit:]]')),'x',''))
         )
     ) B 
     LEFT JOIN RM.SALUNO SA ON SA.RA = B.CD_ALUNO
     LEFT JOIN RM.PPESSOA  A ON A.CODIGO = SA.CODPESSOA
 ORDER BY A.NOME ASC, B.DATAVENCIMENTO ASC";
 
	$stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_USUARIO',  $dados['cd_usuario']);

    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);

}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}


?>