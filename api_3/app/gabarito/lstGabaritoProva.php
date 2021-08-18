<?php 
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'cd_usuario' => $params['p_cd_usuario'],
    'bimestre'   => $params['p_bimestre'],
    'num_prova'  => $params['p_num_prova'],
    'periodo'    => '2020/1'
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);

try{

    $sql = 'SELECT NF.CD_PROVA, NF.cd_aluno, P.NUM_PROVA, p.CHAMADA, 
                    P.DT_PROVA, P.TITULO, BD_SICA.F_AVAL_PROVA_DISCIPLINAS(P.CD_PROVA) AS DISCIPLINA, I.FEZ_PROVA, 
                    P.HR_INICIO, P.HR_FIM, P.FL_FORMATO, Q.POSICAO, Q.RESPOSTA, Q.CORRETA, Q.VALOR
        FROM BD_SICA.AVAL_PROVA_ALUNO_DISC NF
        INNER JOIN BD_SICA.AVAL_PROVA P ON P.CD_PROVA = NF.CD_PROVA OR P.CD_PROVA_PAI IS NULL AND P.CD_PROVA_PAI = NF.CD_PROVA
        INNER JOIN BD_SICA.AVAL_PROVA_INSCRITOS I ON I.CD_PROVA = P.CD_PROVA OR I.CD_PROVA_VERSAO IS NOT NULL AND I.CD_PROVA_VERSAO = P.CD_PROVA
        INNER JOIN BD_SICA.AVAL_PROVA_ALUNO_QUESTAO Q ON Q.CD_PROVA = I.CD_PROVA_VERSAO OR Q.CD_PROVA = P.CD_PROVA
        WHERE NF.CD_ALUNO = :CD_ALUNO AND
        P.BIMESTRE   = :BIMESTRE AND
        I.FEZ_PROVA  = 1 AND
        P.PERIODO = :PERIODO AND
        (P.NUM_PROVA = :NUM_PROVA OR :NUM_PROVA IS NULL) ORDER BY Q.POSICAO ASC';

    //P.PERIODO    = (SELECT CL_PERIODO_ATUAL FROM BD_SICA.CONFIGURACAO) AND    
   
    $stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_ALUNO',    $dados['cd_usuario']);
    oci_bind_by_name($stid, ':BIMESTRE',    $dados['bimestre']);
    oci_bind_by_name($stid, ':NUM_PROVA',   $dados['num_prova']);
    oci_bind_by_name($stid, ':PERIODO',     $dados['periodo']);

    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);

}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}


?>