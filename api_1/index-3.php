
<?php

define('user', 'SISWEB');
define('pass', '17Sec001!xx');
define('tns', '10.228.20.18/bdiseculo.seculomanaus.com.br');///bdiseculo.seculomanaus.com.br
define('encode', 'AL32UTF8');


$p_encoding  = $_GET['encoding'];
$ord 		 = 3;
$id 		 = 'dsadhasudhasuhdsa'; 

echo gettype($p_encoding);
print_r($p_encoding);

$conn   = oci_connect(user, pass, tns, encode);




if (!$conn) {
    echo "Erro na Conexão com o Banco";
    $m = oci_error();
    trigger_error(htmlentities($m['message']), E_USER_ERROR);
}

// $sql = 'INSERT INTO BD_APLICACAO.user_encoding
//    select ROWNUM, p_id AS ID , to_number(replace(TRIM(AMOSTRA_ENCODES),''.'','','')) AS ENCODE
//         from (
//          select  regexp_substr( :P_ENCODES,''[^,]+'', 1, level)
//                       AS AMOSTRA_ENCODES
//                       from dual
//                       connect by regexp_substr(:P_ENCODES, ''[^,]+'', 1, level) is not null

//               )'; 


$sql = 'INSERT INTO  BD_APLICACAO.USER_ENCODING (ORD, ID, ENCODE_BASE) VALUES (:ORD, :ID, :ENCODE_BASE)';
$stid   = oci_parse($conn, $sql);
oci_bind_by_name($stid, ':ORD',$p_encoding);
oci_bind_by_name($stid, ':ID',$p_encoding);
oci_bind_by_name($stid, ':ENCODE_BASE',$p_encoding);
oci_execute($stid);


// $result['RETORNO'] = array();
// while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
// 	// if($row['CD_USUARIO'] != $p_id){
// 	// 	$result['RETORNO'] = "N";
// 	// }else{
// 	// 	$result['RETORNO'] = "S";
// 	// }

// echo "[".json_encode($result)."]";
		
// }	

// oci_free_statement($stid);
// oci_close($conn);

?>