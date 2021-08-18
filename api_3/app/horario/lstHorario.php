<?php
require_once('../../config/conection.php');

$p_cd_usuario = @$_POST['p_cd_usuario'];

/*
    ra
*/

if(empty($p_cd_usuario)){
    echo json_encode(array('Erro' => 'Você não tem permissão de acesso!'));
    exit();
}

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'cd_usuario' => $p_cd_usuario
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);

try{

    $sql    = 'SELECT DESCRICAO, DIASEMANA, HORAINICIAL, HORAFINAL, NOME FROM RM.VW_ALUNO_HORARIO 
    WHERE RA = :P_CD_USUARIO ORDER BY HORAINICIAL ASC'; 
	$stid   = oci_parse($conn, $sql);

    oci_bind_by_name($stid, ':P_CD_USUARIO', $p_cd_usuario);
    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

    echo json_encode($result);


}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}

?>