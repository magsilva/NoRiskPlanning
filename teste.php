<?php

include("./inc/nusoap/nusoap.php");

$address = array(
	'street' => '123 Freezind',
	'city' => 'Nome',
	'state' => 'Alaska',
	'zip' => 1234,
	'phonenumbers' => array('home' => '12243243', 'mobile' => '0982340')
);

$s = new soapval('myAddress', 'address', $address, '', 'http://myNamespace.com');

print "<xmp>".$s->serialize()."</xmp>";


?>
