<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);


$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'opcao' => $params['p_opcao']
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados['opcao']);
curl_exec($iniciar);

$conn = oci_connect(user, pass, tns, encode);

try{

    $sql = "SELECT C.*, T.DC_TIPO, O.DC_OPCAO, CS.* FROM RM.ZMDCARDAPIO C
    JOIN RM.ZMDCARDAPIO_SEMANA CS ON CS.ID_CARDAPIO=C.ID_CARDAPIO
    JOIN RM.ZMDCARDAPIO_TIPO T ON T.ID_TIPO=CS.ID_TIPO
    JOIN RM.ZMDCARDAPIO_OPCAO O ON O.ID_OPCAO=CS.ID_OPCAO
    ORDER BY CS.ORD";
    
    $stid   = oci_parse($conn, $sql); 
    oci_execute($stid);
    oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    echo json_encode($result);

}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}

?>