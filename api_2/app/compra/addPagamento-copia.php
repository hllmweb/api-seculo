<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');

require_once('../../config/conection.php');

$params = json_decode(file_get_contents('php://input'), TRUE);


$numeroEstabelecimento    = "1051047860";
$chaveCielo               = "dd8b9b4c3fa1ed6594afff1fa2d4473924153eb01a04e4f9f3c873fe44245512";
$urlProducao              = "https://ecommerce.cbmp.com.br/servicos/ecommwsec.do";


$dados = array(
    'produto'               => $params['p_produto'],
    'cod_bandeira'          => $params['p_cod_bandeira'],
    'form_pagamento'        => $params['p_form_pagamento'],
    'num_cartao'            => $params['p_num_cartao'],
    'cvv'                   => $params['p_cvv'],
    'nome'                  => $params['p_nome'],
    'vencimento'            => $params['p_vencimento'],
    'data_atual'            => date('Y-m-d\TH:i:s'),
    'qtd_parcela'           => 1,
    'metodo_autorizacao'    => 3,
    'captura_automatica'    => true,
    'valor_total'           => $params['p_valor_total']
);


$p_vencimento               = explode("/", $dados['vencimento']);
$p_valor_total              = str_replace(",","", $dados['valor_total']);
$p_form_pagamento           = ($dados['form_pagamento'] == "A") ? "D" : "C"; //se é debito ou credito

// $p_produto 				=  @$_POST['p_produto'];
// $p_cod_bandeira 		=  @$_POST['p_cod_bandeira'];
// $p_form_pagamento 		= (@$_POST['p_form_pagamento'] == "A") ? "D" : "C"; //se é debito ou credito
// $p_num_cartao 			= @$_POST['p_num_cartao'];
// $p_cvv			 		= @$_POST['p_cvv'];
// $p_nome 				= @$_POST['p_nome'];
// $p_vencimento 	  		= explode("/", @$_POST['p_vencimento']);  
// $p_data_atual 			= date('Y-m-d\TH:i:s');
// $p_qtd_parcela 			= 1;
// $p_metodo_autorizacao 	= 3;
// $p_captura_automatica   = true;
// $p_valor_total 			= str_replace(",","",@$_POST['p_valor_total']);


#tabela de bandeiras
switch ($dados['cod_bandeira']){
    case "visa":
        $codBandeira = 1;
        break;
    case "mastercard":
        $codBandeira = 2;
        break;
    case "elo":
        $codBandeira = 3;
        break;
    case "amex":
        $codBandeira = 5;
        break;
}   

#caso cvv for vazio ou null
if($dados['cvv'] == null || $dados['cvv'] == ""){
    $p_indicador = "0";
}else if ($dados['cod_bandeira'] == "mastercard"){
    $p_indicador = "1";
}else {
    $p_indicador = "1";
}


#gerando xml de autenticação
$p_codigo = "829829"; //codigo do aluno + id do produto
$string = '<?xml version="1.0" encoding="ISO-8859-1"?> 
<requisicao-transacao id="'.$p_codigo.'" versao="1.2.1">
<dados-ec>
      <numero>'.$numeroEstabelecimento.'</numero>
      <chave>'.$chaveCielo.'</chave>
</dados-ec>
<dados-portador>
    <numero>'.$dados['num_cartao'].'</numero>
    <validade>'.$p_vencimento[1].$p_vencimento[0].'</validade>
    <indicador>'.$p_indicador.'</indicador>
    <codigo-seguranca>'.$dados['cvv'].'</codigo-seguranca>
    <nome-portador>'.$dados['nome'].'</nome-portador>
</dados-portador>
<dados-pedido>
    <numero>'.$dados['codigo'].'</numero>
    <valor>'.$dados['valor_total'].'</valor>
    <moeda>986</moeda>
    <data-hora>'.$dados['data_atual'].'</data-hora>
    <descricao>www.seculomanaus.com.br</descricao>
    <idioma>PT</idioma>
</dados-pedido>
<forma-pagamento>
    <bandeira>'.$codigoBandeira.'</bandeira>
    <produto>'.$dados['form_pagamento'].'</produto>
    <parcelas>'.$dados['qtd_parcela'].'</parcelas>
</forma-pagamento>
    <autorizar>'.$dados['metodo_autorizacao'].'</autorizar>
    <capturar>'.$dados['captura_automatica'].'</capturar>
</requisicao-transacao>';


$curl = curl_init();
curl_setopt( $curl , CURLOPT_HEADER , 0 );
curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt( $curl , CURLOPT_RETURNTRANSFER , 1 );
curl_setopt( $curl , CURLOPT_FOLLOWLOCATION , 1 );
curl_setopt( $curl , CURLOPT_URL , $urlProducao);
curl_setopt( $curl , CURLOPT_POST , 1 );
curl_setopt( $curl , CURLOPT_POSTFIELDS , http_build_query(array('mensagem' => $string)));

$string = curl_exec($curl);
$xml = simplexml_load_string($string);
curl_close($ch);

//checa se retornou o tid
if ($xml->tid){
    $tid_msg = 'TID da transação: '.$xml->tid.'<br>';
    if($xml->captura->codigo == '6' AND $xml->autorizacao->codigo == '6'){
        $status_msg = 'Transação autorizada pela operadora do cartão!';
    }else{
        $status_msg = 'Transação não autorizada: '.$xml->autorizacao->mensagem.'.';
    }       
}else{
    $tid_msg = 'Transação não autorizada sem tid: '.$xml->mensagem;
}

$dados = array(
	'tid_msg' 	 	=> $tid_msg,
	'status_msg' 	=> $status_msg,
	'valor_total'	=> $dados['valor_total']
);

echo json_encode($dados);
?>