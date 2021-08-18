<?php 

require_once('../config/conection.php');

$p_id  			= @$_POST['id'];
$p_id_loja      = 1; 

/*
    p_id_loja = {
        1 = cantina
        2 = loja
    }
*/

if(empty($p_id))
{
    echo json_encode(array('Erro'=>'Você não tem permissão de acesso!'));
    exit();
}
$iniciar = curl_init();
curl_setopt($iniciar, CURLOPT_RETURNTRANSFER, true);
$dados = array(
    'p_id_loja' 	=> $p_id_loja
);

curl_setopt($iniciar, CURLOPT_POST, true);
curl_setopt($iniciar, CURLOPT_POSTFIELDS, $dados);
curl_exec($iniciar);

$conn   = oci_connect(user, pass, tns, encode);

try{
    $sql    = 'BEGIN BD_PDV.GET_PRODUTOS_TERMINAL2(:P_ID_LOJA, :RC1); END;';
    $stid   = oci_parse($conn, $sql);
    $cursor = oci_new_cursor($conn);

    oci_bind_by_name($stid, ':P_ID_LOJA', $p_id_loja);
    oci_bind_by_name($stid, ':RC1', $cursor, -1, OCI_B_CURSOR);
    oci_execute($stid);
    oci_execute($cursor, OCI_DEFAULT);
    oci_fetch_all($cursor, $result, null, null, OCI_FETCHSTATEMENT_BY_ROW);
     
        
    foreach($result as $row)
    {
        if($row['CD_NATUREZA'] == $p_id)
        {
            $filtered_array[] = $row;
        }
    }

    echo json_encode($filtered_array);

}catch(Exception $e){
    echo 'Erro: '.$e->getMessage();
}


?>