<?php

require("./inc/script_inicialization.php");
include "./inc/nrp_api.php";

require_once('./inc/nusoap/nusoap.php');

/* Setup the WSDL Server */
$NAMESPACE = 'http://notsafe.icmc.usp.br/norisk/';
 
$server = new soap_server;
/*$server->configureWSDL('NRP', $NAMESPACE);
$server->wsdl->schemaTargetNamespace = $NAMESPACE;

$server->configureWSDL('nrpwsdl', 'urn:nrpwsdl');*/

// Register the data structures used by the service

/*$server->wsdl->addComplexType(
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
    'membersArray',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:group.member[]')
    ),
    'tns:group.member'
);

$server->wsdl->addComplexType(
    'group',
    'complexType',
    'struct',
    'all',
    '',
    array(
	'group.id' => array('name' => 'group.id', 'type' => 'xsd:int'),
	'group.name' => array('name' => 'group.name', 'type' => 'xsd:string'),
	'group.category' => array('name' => 'group.category', 'type' => 'xsd:string'),
 	'group.description' => array('name' => 'group.description', 'type' => 'xsd:string'),
 	'group.members' => array('name' => 'group.members', 'type' => 'tns:membersArray')
    )
);

$server->wsdl->addComplexType(
    'groupsArray',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:group[]')
    ),
    'tns:groupsArray'
);

$server->wsdl->addComplexType(
    'schedule',
    'complexType',
    'struct',
    'all',
    '',
    array(
	'groups' => array('name' => 'groups', 'type' => 'tns:groupsArray')
    )
);*/

/* Register the method to return a single ComplexType1 */
/*$server->register('GetAppointments');*/

/*$server->register(
    'GetAllGroups',
    array(),
    array('return'=>'tns:schedule'),
    $NAMESPACE);*/

$server->register('GetAllGroups');

/* 
 * Declare the functions which are called when the above methods are used 
 */
function GetAppointments($account_id, $beg_date, $end_date)
{
	global $bd;
	global $cfg;

        $query = "SELECT * FROM accounts WHERE account_id = '$account_id'";
        $result = $bd->Query($query);
        $role = $bd->FetchResult($result, 0, 'role');
        $var_type = $role . '_type';
        $var_color = $role . '_color';
        $var_image = $role . '_icon';
        $array_type = $cfg[$var_type];
        $array_color = $cfg[$var_color];
        $array_image = $cfg[$var_image];

        $apps = Retrieve_Appointments($account_id, $beg_date, $end_date, '', '', '',
                $cfg['time'], $array_type, $array_color, $array_image, $cfg['days'], $fake, $bd);

	return ($apps);
}

function GetAllGroups()
{
	global $bd;

	$groups = List_Groups('', '', '', '', 0, $bd);

	$groups_aux = array();
	for ($i = 0; $i < count($groups); $i++)
	{
		$group = $groups[$i];
		$groups_aux[$i]["group.id"] = $group[0];
		$groups_aux[$i]["group.name"] = $group[1];
		$groups_aux[$i]["group.category"] = $group[2];
		$groups_aux[$i]["group.acronym"] = $group[3];
		$groups_aux[$i]["group.description"] = $group[4];
		$members = $groups[$i][5];
		for ($j = 0; $j < count($members); $j++)
		{
			$groups_aux[$i]["group.members"][$j]["group.member.id"]=$members[$j][0];
			$groups_aux[$i]["group.members"][$j]["group.member.name"]=$members[$j][1];
			$groups_aux[$i]["group.members"][$j]["group.member.membership"]=$members[$j][2];
		}
	}

    $result = array('schedule' => $groups_aux);

	return ($result);
}

/*GetAllGroups();

exit();*/

// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA'])
                        ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
$server->service($HTTP_RAW_POST_DATA);
exit();

?>
