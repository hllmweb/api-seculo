<?php

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);


$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'cd_usuario' 		=> $params['p_cd_usuario'],
    'usu_senha'         => $params['p_usu_senha']
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

        $sql    = 'SELECT * FROM BD_SICA.VW_LOGIN_APP WHERE USU_LOGIN = :CD_USUARIO AND USU_SENHA = :USU_SENHA'; 
        $stid   = oci_parse($conn, $sql);

        oci_bind_by_name($stid, ':CD_USUARIO', $dados['cd_usuario']);
        oci_bind_by_name($stid, ':USU_SENHA',  $dados['usu_senha']);
        oci_execute($stid);
        oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);

        
        $sql2    = 'SELECT * FROM RM.VW_ALUNO_RESP_RM_GERAL WHERE (CPF_RESPONSAVEL = :CPF_RESPONSAVEL OR RA = :RA) AND IDPERLET = 2';
        $stid2   = oci_parse($conn, $sql2); 

        oci_bind_by_name($stid2, ':CPF_RESPONSAVEL', $dados['cd_usuario']);
        oci_bind_by_name($stid2, ':RA', $dados['cd_usuario']);

        oci_execute($stid2);
        oci_fetch_all($stid2, $result2, null, null, OCI_FETCHSTATEMENT_BY_ROW);


        $response = [];

        if($result){
            $response = [
                'user' => $result,
                'students' => $result2
            ];
        }


        echo json_encode($response);
    


    /*

    // $response = [];

    // if($result){
    //     $response = [
    //         'user' => $result,
    //         'students' => $result2
    //     ];
    // }

    // echo json_encode($response);*/


}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}

?>
