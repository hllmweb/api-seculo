<?php
require_once('../config/conection.php');

$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
curl_exec($iniciar);
$conn   = oci_connect(user, pass, tns, encode);
$sql = "SELECT 'arquivos/'||T1.CD_NATUREZA||'.png' AS IMAGEM, T1.CD_NATUREZA, N.DESCRICAO FROM RM.GCONSIST N 
        LEFT JOIN RM.ZMDDET001 T1 ON T1.CD_NATUREZA = TO_NUMBER(N.CODCLIENTE) 
        WHERE N.CODTABELA = '2' AND N.CODCOLIGADA = 1 AND T1.CD_NATUREZA NOT IN(0,6,7,8,10,11)";

$stid   = oci_parse($conn, $sql);
oci_execute($stid);
oci_fetch_all($stid, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
echo json_encode($result);

?>