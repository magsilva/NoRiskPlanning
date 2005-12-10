<?php

require("./inc/script_inicialization.php");
include "./inc/nrp_api.php";
require_once('./inc/nusoap/nusoap.php');

// Create the server instance
$server = new soap_server();

// Initialize WSDL support
$server->configureWSDL('nrpwsdl', 'urn:nrpwsdl');

// Register the data structures used by the service

$server->wsdl->addComplexType(
    'group.member',
    'complexType',
    'struct',
    'all',
    '',
    array(
	'group.member.id' => array('name' => 'group.member.id', 'type' => 'xsd:string'),
 	'group.member.name' => array('name' => 'group.member.name', 'type' => 'xsd:string'),
	'group.member.membership' => array('name' => 'group.member.membership', 'type' => 'xsd:string')
    )
);

$server->wsdl->addComplexType(
    'groups',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
	array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:group[]')
    ),
    'tns:group'
);

// Register the method to expose
$server->register('hello',                    // method name
    array('person' => 'tns:Person'),          // input parameters
    array('return' => 'tns:SweepstakesGreeting'),    // output parameters
    'urn:hellowsdl2',                         // namespace
    'urn:hellowsdl2#hello',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Greet a person entering the sweepstakes'        // documentation
);
// Define the method as a PHP function
function hello($person) {
    $greeting = 'Hello, ' . $person['firstname'] .
                '. It is nice to meet a ' . $person['age'] .
                ' year old ' . $person['gender'] . '.';
    
    $winner = $person['firstname'] == 'Scott';

    return array(
                'greeting' => $greeting,
                'winner' => $winner
                );
}
// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>
