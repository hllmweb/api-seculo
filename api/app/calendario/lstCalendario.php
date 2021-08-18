<?php 
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);
// $p_turma = @$_POST['p_turma'];

/*
    turma 
*/

// if(empty($p_turma)){
//     echo json_encode(array('Erro' => 'Você não tem permissão de acesso!'));
//     exit();
//}

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'turma'         => $params['p_turma'],
    'cd_usuario'    => $params['p_cd_usuario']
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);

try{

    /*$sql    = 'SELECT C.CD_TURMA, C.DC_CALENDARIO, C.DATA, C.NR_DIAS, C.DC_COLOR, C.PESO, C.NOTA_PROVA, C.INFO_PROVA, C.ANEXO
    FROM BD_SICA.VW_AES_CALENDARIOS C WHERE C.CD_TURMA IS NULL OR C.CD_TURMA = :CD_TURMA ORDER BY C.DATA, C.ORDEM';*/

    $sql = "SELECT
        '' ra,
        PC.CD_DISCIPLINA,
        pc.cd_turma cd_turma, 
        NM_DISC_RED AS DISCIPLINA,
        CASE 
        WHEN EN.NM_MINI = 'N1' THEN  'TURMA: ' || PC.CD_TURMA || ' - ' || 'ATIVIDADE N1 - ' || TRIM(NM_DISC_RED)
        WHEN EN.NM_MINI = 'N2' THEN  'TURMA: ' || PC.CD_TURMA || ' - ' || 'AVALIAÇÃO N2 - ' || TRIM(NM_DISC_RED)
        WHEN TRIM(NM_DISC_RED) IS NULL THEN EN.DC_TIPO_NOTA ELSE EN.DC_TIPO_NOTA || ' - ' || TRIM(NM_DISC_RED)
        END AS DC_CALENDARIO,
        PC.DT_PROVA AS DATA,
        1 AS NR_DIAS,
        CASE WHEN EN.NM_MINI = 'N2' then '#009933' WHEN EN.NM_MINI = 'N1' THEN '#C0C4E4' ELSE '#FFFFFF' end AS DC_COLOR,
        1 AS ORDEM,
        '1' AS PESO,
        SP.VALOR AS NOTA_PROVA,
        BD_APLICACAO.F_CONTEUDO_PROVA(SPC.CODCOLIGADA, SPC.IDTURMADISC, SPC.CODETAPA, SPC.CODPROVA) AS INFO_PROVA,
        'http://seculomanaus.com.br/componentes/portal/calendario/anexo/inicio' AS ANEXO
FROM  BD_SICA.AVAL_PROVA_CALENDARIO PC
JOIN BD_SICA.VW_CL_ESTRUT_NOTA EN ON PC.NUM_NOTA = EN.NUM_NOTA AND
PC.CD_TIPO_NOTA = EN.CD_TIPO_NOTA AND
(
   (PC.CD_CURSO=2 AND EN.CD_ESTRUTURA = 21)OR
   (PC.CD_CURSO=33 AND EN.CD_ESTRUTURA = 22)OR
   (PC.CD_CURSO=3 AND EN.CD_ESTRUTURA = 23)
)
LEFT JOIN BD_SICA.CL_DISCIPLINA D ON D.CD_DISCIPLINA = PC.CD_DISCIPLINA
LEFT JOIN BD_SICA.AVAL_PROVA P ON P.CD_PROVA = PC.CD_PROVA
LEFT JOIN RM.SPROVASCOMPL SPC ON SPC.IDTURMADISC = PC.IDTURMADISC_RM 
                      AND SPC.CODETAPA = PC.CODETAPA_RM 
                      AND SPC.CODPROVA = PC.CODPROVA_RM
LEFT JOIN RM.SPROVAS SP ON SP.IDTURMADISC = SPC.IDTURMADISC 
                      AND SP.CODETAPA = SPC.CODETAPA 
                      AND SP.CODPROVA = SPC.CODPROVA
     
WHERE PC.PERIODO >= TO_CHAR(SYSDATE,'YYYY')||'/1'
AND PC.DT_PROVA IS NOT NULL
AND PC.FL_ATIVO = 1
and pc.cd_turma = :CD_TURMA";



    $stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_TURMA', $dados['turma']);
    oci_bind_by_name($stid, ':CD_USUARIO', $dados['cd_usuario']);
    
    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);

}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}


?>