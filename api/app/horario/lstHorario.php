<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

//$p_cd_usuario = @$_POST['p_cd_usuario'];

$params = json_decode(file_get_contents('php://input'), TRUE);

/*
    ra
*/

/*if(empty($p_cd_usuario)){
    echo json_encode(array('Erro' => 'Você não tem permissão de acesso!'));
    exit();
}*/

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'cd_usuario'        => $params['p_cd_usuario']
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_setopt($iniciar, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: '.strlen($dados))                                                                       
);   
curl_exec($iniciar);

$conn   = oci_connect(user, pass, tns, encode);


try{

    /*$sql    = 'SELECT DESCRICAO, DIASEMANA, HORAINICIAL, HORAFINAL, NOME, RA FROM RM.VW_ALUNO_HORARIO 
    WHERE RA = :P_CD_USUARIO ORDER BY HORAINICIAL ASC';*/

    $sql    = "SELECT rownum ID, DATA, DESCRICAO, 
                      DIASEMANA, 
                      case when diasemana = 2 then 'SEGUNDA-FEIRA'
                        when diasemana = 3 then 'TERÇA-FEIRA'
                        when diasemana = 4 then 'QUARTA-FEIRA'
                        when diasemana = 5 then 'QUINTA-FEIRA'
                        when diasemana = 6 then 'SEXTA-FEIRA'
                    end ds_diasemana,
                HORAINICIAL, HORAFINAL, NOME, RA, CONTEUDOEFETIVO FROM RM.VW_ALUNO_HORARIO 
                WHERE RA = :P_CD_USUARIO AND TRUNC(DATA) BETWEEN trunc(sysdate,'w') AND trunc(sysdate,'w')+6 ORDER BY HORAINICIAL ASC"; 

	$stid   = oci_parse($conn, $sql);

    oci_bind_by_name($stid, ':P_CD_USUARIO', $dados['cd_usuario']);
    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

    echo json_encode($result);


}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}

?>