<?php

require_once('./inc/nusoap/nusoap.php');

//$parametros = array('account_id'=>'apfreire', 'beg_date'=>'2005-01-01', 'end_date' => '2005-07-29');
$parametros = array();
$clientsoap = new soapclient('http://notsafe.icmc.usp.br/norisk/wsdl.php');
$resultado = $clientsoap->call('GetAllGroups', $parametros);
//$resultado = $clientsoap->call('GetAppointments', $parametros);

echo "webservice";

if ($clientsoap->fault)
	echo "erro no webservice". $clientsoap->faultstring;
else
	print_r($resultado);

echo $clientsoap->response;


?>
