<?php

require("./inc/script_inicialization.php");
include "./inc/nrp_api.php";


/* No Risk Planning namespace */
$ns = 'http://notsafe.icmc.usp.br/norisk/wsdl.php';

require_once('./inc/nusoap/nusoap.php');

/* Setup the WSDL Server */
$server = new soap_server();
$server->debug_flag=false;

//include "./inc/wsdl/schema.php";

//include "./inc/wsdl/operations.php";
include "./inc/wsdl/operations1.php";

include "./inc/wsdl/implementation.php";

// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA'])
                        ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
$server->service($HTTP_RAW_POST_DATA);
exit();

?>
