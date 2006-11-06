<?php

/* Implementation of the No Risk Web Service.
 *
 * Contributors (alphabetic order):
 *     Alexandre Michetti Manduca (a.michetti@gmail.com)
 *     Felipe ()
 *     Tati ()
 *     Vinicius ()
*/ 

// Include the necessary files
require_once('services.php');      // Functionalities implementation
require_once('nusoap/nusoap.php'); // NuSOAP lib

// 0/1: Deactivate/Activate debug information inside SOAP messages
// note: The debug information goes inside XML comments
$debug = 1;

// Initialize the service
$server = new soap_server;

// Initialize WSDL
$server->configureWSDL('norisk', 'urn:norisk');


// Register the functionalities and types this service will have.
// This will also create the WSDL of the service.
//
// note1: Try to put together the types you define and the
//        functionality definition which uses it.
//
// note2: Try to keep an order that makes senses here, because the NuSOAP will
//        automaticaly gerate a web page with the services definition. So keep
//        the login/logout function first and try to keep related
//        functionalities together.


// Functionality: login
$server->register(
     // Functionality name
     'login',
     // Input parameters
     array(
         'account_id' => 'xsd:string',
         'password' => 'xsd:string',
     ),
     // Output parameters
     array('complete_sess_id' => 'xsd:string'),
     // Name that solely identifies the functionality
     'urn:norisk',
     // Name that solely identifies the functionality within the service
     'urn:norisk#login',
     // XML style to use (rpc, document)
     'rpc',
     // Codification (encoded, literal)
     'encoded',
     // Documentation
     'Login at NoRisk\'s Web Service'
);
    
// Functionality: logout
$server->register(
     // Functionality name
     'logout',
     // Input parameters
     array('complete_sess_id' => 'xsd:string'),
     // Output parameters
     array('result' => 'xsd:string'),
     // Name that solely identifies the functionality
     'urn:norisk',
     // Name that solely identifies the functionality within the service
     'urn:norisk#logout',
     // XML style (rpc, document)
     'rpc',
     // Codification (encoded, literal)
     'encoded',
     // Documentation
     'Logout from NoRisk\'s Web Service'
);
    
// Functionality: newAppointment
$server->register(
     // Functionality name
     'newAppointment',
     // Input parameters
     array(
         'complete_sess_id' => 'xsd:string',
         'description' => 'xsd:string',
         'type' => 'xsd:string',
         'date' => 'xsd:date',
         'beg_time' => 'xsd:time',
         'end_time' => 'xsd:time',
         'url' => 'xsd:string',
     ),
     // Output parameters
     array('result' => 'xsd:int'),
     // Name that solely identifies the functionality
     'urn:norisk',
     // Name that solely identifies the functionality within the service
     'urn:norisk#newAppointment',
     // XML style (rpc, document)
     'rpc',
     // Codification (encoded, literal)
     'encoded',
     // Documentation
     'Insert an Appointment at NoRisk'
);
    
// Functionality: delAppointment
$server->register(
     // Functionality name
     'delAppointment',
     // Input parameters
     array(
         'complete_sess_id' => 'xsd:string',
	     'app_id' => 'xsd:bigint'
     ),
     // Output parameters
     array('result' => 'xsd:int'),
     // Name that solely identifies the functionality
     'urn:norisk',
     // Name that solely identifies the functionality within the service
     'urn:norisk#delAppointment',
     // XML style (rpc, document)
     'rpc',
     // Codification (encoded, literal)
     'encoded',
     // Documentation
     'Delete an Appointment at NoRisk'
);

// Functionality: updateAppointment
$server->register(
     // Functionality name
     'updateAppointment',
     // Input parameters
     array(
         'complete_sess_id' => 'xsd:string',
         'app_id' => 'xsd:int',
         'description' => 'xsd:string',
         'type' => 'xsd:string',
         'date' => 'xsd:date',
         'beg_time' => 'xsd:time',
         'end_time' => 'xsd:time',
         'url' => 'xsd:string'
     ),
     // Output parameters
     array('result' => 'xsd:int'),
     // Name that solely identifies the functionality
     'urn:norisk',
     // Name that solely identifies the functionality within the service
     'urn:norisk#updateAppointment',
     // XML style (rpc, document)
     'rpc',
     // Codification (encoded, literal)
     'encoded',
     // Documentation
     'Update an Appointment at NoRisk'
);

// Functionality: newPerson
$server->register(
     // Functionality name
    'newPerson',
     // Input parameters
    array(
        'complete_sess_id' => 'xsd:string',
        'account_id' => 'xsd:string',
        'name' => 'xsd:string',
        'comments' => 'xsd:string',
        'email' => 'xsd:string',
        // Id do departamento
        'dep_id' => 'xsd:int',
        'url' => 'xsd:string',
        'password' => 'xsd:string',
        // Id da categoria
        'category' => 'xsd:int',
    ),
     // Output parameters
    array('result' => 'xsd:int'),
    // Name that solely identifies the functionality
    'urn:norisk',
    // Nome que identifica unicamente a funcionalidade do servico
    'urn:norisk#newPerson',
    // XML style
    'rpc',
    // Codification
    'encoded',
    // Documentation
    'Insert a new Person at NoRisk'
);


// Functionality deletePerson
$server->register(
    // Functionality name
    'deletePerson',
    // Input parameters
    array(
        'complete_sess_id' => 'xsd:string',
        'account_id' => 'xsd:string'
    ),
    // Output parameters
    array('result' => 'xsd:int'),
    // Name that solely identifies the functionality
    'urn:norisk',
    // Name that solely identifies the functionality within the service
    'urn:norisk#deletePerson',
    // XML style (rpc, document)
    'rpc',
    // Codification (encoded, literal)
    'encoded',
    // Documentation
    'Deletes a given user, and all its dependencies.'
);



// Functionality: listAppointments
// By: Alexandre Michetti Manduca (a.michetti@gmail.com)

// WSDL Types definition
// Appointment (Struct)
$server->wsdl->addComplexType(
    'Appointment',  // Nome do Tipo
    'complexType', // Classe do Tipo (complexType, simpleType, attribute)
    'struct',      // Correspondente ao tipo em php
    'all',         // Formato (all, sequence, choice)
    '',            // restrictionBase (pode ser '')
    array(
        'varstring' => array('name'=>'varstring', 'type'=>'string'),
        'app_id' => array('name'=>'app_id', 'type'=>'xsd:int'),
        'description' => array('name'=>'description', 'type'=>'xsd:string'),
        'beg_time' => array('name'=>'beg_time', 'type'=>'xsd:time'),
        'end_time' => array('name'=>'end_time', 'type'=>'xsd:time'),
        'date' => array('name'=>'date', 'type'=>'xsd:date'),
        'type' => array('name'=>'type', 'type'=>'xsd:string'),
        'url' => array('name'=>'url', 'type'=>'xsd:string'),
        'owner' => array('name'=>'owner', 'type'=>'xsd:string'),
    )
);

// AppointmentLlist (Array of Appointments)
$server->wsdl->addComplexType(
    'AppointmentsList', // Nome do Tipo
    'complexType',      // Classe do Tipo (complexType, simpleType, attribute)
    'array',            // Correspondente ao tipo em php
    '',                 // Formato (all, sequence, choice)
    'SOAP-ENC:Array',   // restrictionBase ('', SOAP-ENC:Array)
    // Elementos
    array(),
    // Atributos
    array(
        array(
            'ref'=>'SOAP-ENC:arrayType',
            'wsdl:arrayType'=>'Appointment[]'
        )
    ),
    'Appointment'       // Tipo (definido no WSDL) do Array
);

$server->register(
     // Functionality name
     'listAppointments',
     // Input parameters
     array(
         'complete_sess_id' => 'xsd:string',
         'beg_date' => 'xsd:date',
         'end_date' => 'xsd:date',
         'beg_time' => 'xsd:time',
         'end_time' => 'xsd:time',
         'type' => 'xsd:string',
     ),
     // Output parameters
     array('appointments' => 'tns:AppointmentsList'),
     // Name that solely identifies the functionality
     'urn:norisk',
     // Name that solely identifies the functionality within the service
     'urn:norisk#listAppointments',
     // XML style (rpc, document)
     'rpc',
     // Codification (encoded, literal)
     'encoded',
     // Documentation
     'List all appointments (except the weekly ones) of the log in user'
);


// Take the HTTP POST data ($HTTP_RAW_POST_DATA) and sends it to the NuSOAP
// ->service function, so it can decode it (because it was encoded with the
// SOAP protocol) and call the appropriate functions (defined in services.php)
// with the appropriate parameters.
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);

?>
