<?php 
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'cd_usuario'        => $params['p_cd_usuario'],
    'id_notificacao'    => $params['p_id_notificacao'],
    'status_confirmacao' => $params['p_status_confirmacao']
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);


try{

    if($dados['status_confirmacao'] == 'S'){
      $mensagem = "confirmação efetuada!";
    }elseif($dados['status_confirmacao'] == 'N'){
      $mensagem = "confirmação não efetuada!";
    }

    $result = array(
      'mensagem' => $mensagem
    );
    echo '['.json_encode($result).']';


     

    /*$sql = "";
    $stid   = oci_parse($conn, $sql); 

    oci_bind_by_name($stid, ':CD_USUARIO',    $dados['cd_usuario']);

    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);*/

}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}

?>