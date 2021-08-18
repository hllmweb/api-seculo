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
// }


$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'turma' => $params['p_turma']
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);

try{

    $sql    = 'SELECT C.CD_TURMA, C.DC_CALENDARIO, C.DATA, C.NR_DIAS, C.DC_COLOR, C.PESO, C.NOTA_PROVA, C.INFO_PROVA, C.ANEXO
    FROM BD_SICA.VW_AES_CALENDARIOS C WHERE C.CD_TURMA IS NULL OR C.CD_TURMA = :CD_TURMA ORDER BY C.DATA, C.ORDEM';
    $stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_TURMA', $dados['turma']);
    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);

}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}


?>