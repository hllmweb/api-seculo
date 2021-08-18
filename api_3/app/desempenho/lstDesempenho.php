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

    $sql = "SELECT SDISCIPLINA.CODDISC AS CD_DISCIPLINA,
                   SDISCIPLINA.NOME AS NM_DISCIPLINA,
                  'A' AS TIPO,
                  '' AS MEDIA
      /*   AVG(
          RM.F_NOTA_ETAPA_ii('1'=>SMATRICULA.CODCOLIGADA,
                          '1'=>SMATRICULA.IDPERLET,
                          '14001098'=>SMATRICULA.RA,
                          '170'=>SMATRICULA.IDTURMADISC,
                          'EFM0901'=>STURMADISC.CODTURMA,
                          'F0009'=>SDISCIPLINA.CODDISC,
                          '12'=>12)
                          )AS MEDIA */
                               FROM
                                 RM.SMATRICULA  ,
                                 RM.STURMADISC  ,
                                 RM.SDISCIPLINA  ,
                                 RM.SSTATUS  ,
                                 RM.SALUNO  ,
                                 RM.PPESSOA  ,
                                 RM.SPLETIVO  ,
                                 RM.GFILIAL  ,
                                 RM.STIPOCURSO
                                 
                               WHERE
                                 SMATRICULA.CODCOLIGADA = STURMADISC.CODCOLIGADA AND
                                 SMATRICULA.IDTURMADISC = STURMADISC.IDTURMADISC AND
                                 STURMADISC.CODCOLIGADA = SDISCIPLINA.CODCOLIGADA AND
                                 STURMADISC.CODDISC = SDISCIPLINA.CODDISC AND
                                 SSTATUS.CODCOLIGADA = SMATRICULA.CODCOLIGADA AND
                                 SSTATUS.CODSTATUS = SMATRICULA.CODSTATUS AND
                                 SALUNO.CODCOLIGADA = SMATRICULA.CODCOLIGADA AND
                                 SALUNO.RA = SMATRICULA.RA AND
                                 PPESSOA.CODIGO = SALUNO.CODPESSOA AND
                                 SMATRICULA.CODCOLIGADA = SPLETIVO.CODCOLIGADA AND
                                 SMATRICULA.IDPERLET = SPLETIVO.IDPERLET AND
                                 STURMADISC.CODCOLIGADA = GFILIAL.CODCOLIGADA AND
                                 STURMADISC.CODFILIAL = GFILIAL.CODFILIAL AND
                                 SDISCIPLINA.CODCOLIGADA = STIPOCURSO.CODCOLIGADA AND
                                 SDISCIPLINA.CODTIPOCURSO = STIPOCURSO.CODTIPOCURSO AND
                                 
                                 

                                 (
                                   SMATRICULA.CODSTATUS in (15) and 
                                   SMATRICULA.CODCOLIGADA = 1 AND
                                   SMATRICULA.IDPERLET = 2 AND
                                   STURMADISC.CODTURMA NOT LIKE 'EC%' AND
                                   SMATRICULA.RA = :CD_ALUNO AND
                                   SMATRICULA.IDTURMADISCORIGEM IS NULL
                                   

                                   
            )
            
     GROUP BY  SDISCIPLINA.CODDISC, SDISCIPLINA.NOME
         
UNION ALL

SELECT
          SDISCIPLINA.CODDISC AS CD_DISCIPLINA,
          SDISCIPLINA.NOME AS NM_DISCIPLINA,
          'T' AS TIPO,
          '' AS MEDIA
/*         AVG(
          RM.F_NOTA_ETAPA_ii(P_CODCOLIGADA=>SMATRICULA.CODCOLIGADA,
                          P_IDPERLET=>SMATRICULA.IDPERLET,
                          P_RA=>SMATRICULA.RA,
                          P_IDTURMADISC=>SMATRICULA.IDTURMADISC,
                          P_CODTURMA=>STURMADISC.CODTURMA,
                          P_CODDISC=>SDISCIPLINA.CODDISC,
                          P_CODETAPA=>12)
                          )AS MEDIA */
                               FROM
                                 RM.SMATRICULA  ,
                                 RM.STURMADISC  ,
                                 RM.SDISCIPLINA  ,
                                 RM.SSTATUS  ,
                                 RM.SALUNO  ,
                                 RM.PPESSOA  ,
                                 RM.SPLETIVO  ,
                                 RM.GFILIAL  ,
                                 RM.STIPOCURSO
                                 
                               WHERE
                                 SMATRICULA.CODCOLIGADA = STURMADISC.CODCOLIGADA AND
                                 SMATRICULA.IDTURMADISC = STURMADISC.IDTURMADISC AND
                                 STURMADISC.CODCOLIGADA = SDISCIPLINA.CODCOLIGADA AND
                                 STURMADISC.CODDISC = SDISCIPLINA.CODDISC AND
                                 SSTATUS.CODCOLIGADA = SMATRICULA.CODCOLIGADA AND
                                 SSTATUS.CODSTATUS = SMATRICULA.CODSTATUS AND
                                 SALUNO.CODCOLIGADA = SMATRICULA.CODCOLIGADA AND
                                 SALUNO.RA = SMATRICULA.RA AND
                                 PPESSOA.CODIGO = SALUNO.CODPESSOA AND
                                 SMATRICULA.CODCOLIGADA = SPLETIVO.CODCOLIGADA AND
                                 SMATRICULA.IDPERLET = SPLETIVO.IDPERLET AND
                                 STURMADISC.CODCOLIGADA = GFILIAL.CODCOLIGADA AND
                                 STURMADISC.CODFILIAL = GFILIAL.CODFILIAL AND
                                 SDISCIPLINA.CODCOLIGADA = STIPOCURSO.CODCOLIGADA AND
                                 SDISCIPLINA.CODTIPOCURSO = STIPOCURSO.CODTIPOCURSO AND

                                 (
                                   SMATRICULA.CODSTATUS in (15) and 
                                   SMATRICULA.CODCOLIGADA = 1 AND
                                   SMATRICULA.IDPERLET = 2 AND
                                   STURMADISC.CODTURMA NOT LIKE 'EC%' AND
                                   SMATRICULA.IDTURMADISCORIGEM IS NULL                                   
                                   and  STURMADISC.CODTURMA = (SELECT RESP.TURMA FROM RM.VW_ALUNO_RESP_RM_GERAL RESP WHERE RA = :CD_ALUNO)
                                   
            )
GROUP BY  SDISCIPLINA.CODDISC, SDISCIPLINA.NOME     
UNION ALL
SELECT
          SDISCIPLINA.CODDISC AS CD_DISCIPLINA,
          SDISCIPLINA.NOME AS NM_DISCIPLINA,
          'C' AS TIPO,
          '' AS MEDIA
/*         AVG(
          RM.F_NOTA_ETAPA_ii(P_CODCOLIGADA=>SMATRICULA.CODCOLIGADA,
                          P_IDPERLET=>SMATRICULA.IDPERLET,
                          P_RA=>SMATRICULA.RA,
                          P_IDTURMADISC=>SMATRICULA.IDTURMADISC,
                          P_CODTURMA=>STURMADISC.CODTURMA,
                          P_CODDISC=>SDISCIPLINA.CODDISC,
                          P_CODETAPA=>12)
                          )AS MEDIA */
                               FROM
                                 RM.SMATRICULA  ,
                                 RM.STURMADISC  ,
                                 RM.SDISCIPLINA  ,
                                 RM.SSTATUS  ,
                                 RM.SALUNO  ,
                                 RM.PPESSOA  ,
                                 RM.SPLETIVO  ,
                                 RM.GFILIAL  ,
                                 RM.STIPOCURSO,
                                 RM.SHABILITACAOFILIAL SHF 
                               WHERE
                                 SMATRICULA.CODCOLIGADA = STURMADISC.CODCOLIGADA AND
                                 SMATRICULA.IDTURMADISC = STURMADISC.IDTURMADISC AND
                                 STURMADISC.CODCOLIGADA = SDISCIPLINA.CODCOLIGADA AND
                                 STURMADISC.CODDISC = SDISCIPLINA.CODDISC AND
                                 SSTATUS.CODCOLIGADA = SMATRICULA.CODCOLIGADA AND
                                 SSTATUS.CODSTATUS = SMATRICULA.CODSTATUS AND
                                 SALUNO.CODCOLIGADA = SMATRICULA.CODCOLIGADA AND
                                 SALUNO.RA = SMATRICULA.RA AND
                                 PPESSOA.CODIGO = SALUNO.CODPESSOA AND
                                 SMATRICULA.CODCOLIGADA = SPLETIVO.CODCOLIGADA AND
                                 SMATRICULA.IDPERLET = SPLETIVO.IDPERLET AND
                                 STURMADISC.CODCOLIGADA = GFILIAL.CODCOLIGADA AND
                                 STURMADISC.CODFILIAL = GFILIAL.CODFILIAL AND
                                 SDISCIPLINA.CODCOLIGADA = STIPOCURSO.CODCOLIGADA AND
                                 SDISCIPLINA.CODTIPOCURSO = STIPOCURSO.CODTIPOCURSO AND
                                 SHF.IDHABILITACAOFILIAL=RM.SMATRICULA.IDHABILITACAOFILIAL AND 
                                 SHF.CODCURSO IN ('001','002', '003', '004') AND
                                 SHF.CODCOLIGADA=RM.SMATRICULA.CODCOLIGADA AND 

                                 (
                                   SMATRICULA.CODSTATUS in (15) and 
                                   SMATRICULA.CODCOLIGADA = 1 AND
                                   SMATRICULA.IDPERLET = 2 AND
                                   STURMADISC.CODTURMA NOT LIKE 'EC%' AND
                                   SMATRICULA.IDTURMADISCORIGEM IS NULL                                   
                                   and SHF.CODCURSO = '002'

                                   
            )
     GROUP BY  SDISCIPLINA.CODDISC, SDISCIPLINA.NOME";

   
    $stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_ALUNO',    $dados['cd_usuario']);

    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);

}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}

?>