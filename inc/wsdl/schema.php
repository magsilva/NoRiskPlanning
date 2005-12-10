<?

$server->configureWSDL('NRP_WSDL', $ns);
$server->wsdl->schemaTargetNamespace = $ns;
// Create a complex type

$server->wsdl->addComplexType(
    'span_start',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'span_start.day' => array('name' => 'span_start.day', 'type' => 'xsd:int'),
        'span_start.month' => array('name' => 'span_start.month', 'type' => 'xsd:int'),
        'span_start.year' => array('name' => 'span_start.year', 'type' => 'xsd:int'),
        'span_start.day_of_week' => array('name' => 'span_start.day_of_week', 'type' => 'xsd:int'),
    )
);

$server->wsdl->addComplexType(
    'span_end',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'span_end.day' => array('name' => 'span_end.day', 'type' => 'xsd:int'),
        'span_end.month' => array('name' => 'span_end.month', 'type' => 'xsd:int'),
        'span_end.year' => array('name' => 'span_end.year', 'type' => 'xsd:int'),
        'span_end.day_of_week' => array('name' => 'span_end.day_of_week', 'type' => 'xsd:int'),
    )
);

$server->wsdl->addComplexType(
    'appointment.date',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'appointment.date.day' => array('name' => 'appointment.date.day', 'type' => 'xsd:int'),
        'appointment.date.month' => array('name' => 'appointment.date.month', 'type' => 'xsd:int'),
        'appointment.date.year' => array('name' => 'appointment.date.year', 'type' => 'xsd:int'),
    )
);

$server->wsdl->addComplexType(
    'appointment',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'appointment.periodicity' => array('name' => 'appointment.periodicity', 'type' => 'xsd:string'),
        'appointment.description' => array('name' => 'appointment.description', 'type' => 'xsd:string'),
        'appointment.length' => array('name' => 'appointment.length', 'type' => 'xsd:int'),
        'appointment.date' => array('name' => 'appointment.date', 'type' => 'tns:appointment.date'),
        'appointment.date' => array('name' => 'appointment.date', 'type' => 'tns:appointment.date'),
        'appointment.beg_time' => array('name' => 'appointment.beg_time', 'type' => 'xsd:time'),
        'appointment.end_time' => array('name' => 'appointment.end_time', 'type' => 'xsd:time'),
        'appointment.type' => array('name' => 'appointment.type', 'type' => 'xsd:string'),
        'appointment.color' => array('name' => 'appointment.color', 'type' => 'xsd:string'),
        'appointment.image' => array('name' => 'appointment.image', 'type' => 'xsd:string'),
        'appointment.dayofweek' => array('name' => 'appointment.dayofweek', 'type' => 'xsd:int'),
        'appointment.url' => array('name' => 'appointment.url', 'type' => 'xsd:string'),
        'appointment.owner' => array('name' => 'appointment.owner', 'type' => 'xsd:string'),
        'appointment.group' => array('name' => 'appointment.group', 'type' => 'xsd:string'),
        'appointment.authorized' => array('name' => 'appointment.authorized', 'type' => 'xsd:int'),
        'appointment.before' => array('name' => 'appointment.before', 'type' => 'xsd:int'),
        'appointment.after' => array('name' => 'appointment.after', 'type' => 'xsd:int'),
    )
);

$server->wsdl->addComplexType(
	'ArrayAppointment', // Name
	'complexType', // Type Class
	'array', // PHP Type
	'', // Compositor
	'SOAP-ENC:Array', // Restricted Base
	array(),
	array(
		array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:appointment[]')
	),
	'tns:appointment'
);

$server->wsdl->addComplexType(
    'schedule',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'span_start' => array('name' => 'span_start', 'type' => 'tns:span_start'),
        'span_end' => array('name' => 'span_end', 'type' => 'tns:span_end'),
        'appointment' => array('name' => 'appointment', 'type' => 'tns:ArrayAppointment'),
    )
);

$server->wsdl->addComplexType(
    'group.member',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'group.member.name' => array('name' => 'group.member.name', 'type' => 'xsd:string'),
        'group.member.membership' => array('name' => 'group.member.membership', 'type' => 'xsd:int'),
    )
);

$server->wsdl->addComplexType(
	'ArrayGroupMember', // Name
	'complexType', // Type Class
	'array', // PHP Type
	'', // Compositor
	'SOAP-ENC:Array', // Restricted Base
	array(),
	array(
		array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:group.member[]')
	),
	'tns:group.member'
);

$server->wsdl->addComplexType(
	'ArrayGroupMember', // Name
	'complexType', // Type Class
	'array', // PHP Type
	'', // Compositor
	'SOAP-ENC:Array', // Restricted Base
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
        'category' => array('name' => 'category', 'type' => 'xsd:string'),
        'name' => array('name' => 'name', 'type' => 'xsd:string'),
        'acronym' => array('name' => 'span_end', 'type' => 'xsd:string'),
        'description' => array('name' => 'description', 'type' => 'xsd:description'),
        'member' => array('name' => 'member', 'type' => 'tns:ArrayGroupMember'),
    )
);

$server->wsdl->addComplexType(
	'ArrayGroup', // Name
	'complexType', // Type Class
	'array', // PHP Type
	'', // Compositor
	'SOAP-ENC:Array', // Restricted Base
	array(),
	array(
		array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:group[]')
	),
	'tns:group'
);
?>
