<?
/* Register the operations */

/* Register the method to return a single ComplexType1 */
$server->register('GetAppointments',                    // method name
    array('account_id' => 'xsd:string', 'beg_date' => 'xsd:date', 'end_date' => 'xsd:date'),   // input parameters
    array('return' => 'tns:schedule'),    // output parameters
    $ns,                         // namespace
    $ns . '#GetAppointments',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Get the schedule of a given user in a given time span'        // documentation
);

/* Register the method to return a single ComplexType1 */
$server->register('InsertAppointment',                    // method name
    array('account_id' => 'xsd:string', 'date' => 'xsd:date', 'beg_time' => 'xsd:time', 'end_time' => 'xsd:time', 'type' => 'xsd:string'),   // input parameters
    array('return' => 'xsd:boolean'),    // output parameters
    $ns,                         // namespace
    $ns . '#InsertAppointment',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Insert a Common Appointment'        // documentation
);

/* Register the method to return a single ComplexType1 */
$server->register('RemoveAppointment',                    // method name
    array('account_id' => 'xsd:string', 'date' => 'xsd:date', 'beg_time' => 'xsd:time'),   // input parameters
    array('return' => 'xsd:boolean'),    // output parameters
    $ns,                         // namespace
    $ns . '#RemoveAppointment',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Remove an Appointment'        // documentation
);

/* Register the method to return a single ComplexType1 */
$server->register('InsertWeeklyAppointment',                    // method name
    array('account_id' => 'xsd:string', 'day' => 'xsd:string', 'beg_time' => 'xsd:time', 'end_time' => 'xsd:time', 'type' => 'xsd:string'),   // input parameters
    array('return' => 'xsd:boolean'),    // output parameters
    $ns,                         // namespace
    $ns . '#GetInsertWeeklyAppointment',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Insert a Weekly Appointment'        // documentation
);

/* Register the method to return a single ComplexType1 */
$server->register('RemoveWeeklyAppointment',                    // method name
    array('account_id' => 'xsd:string', 'day' => 'xsd:date', 'beg_time' => 'xsd:time'),   // input parameters
    array('return' => 'xsd:boolean'),    // output parameters
    $ns,                         // namespace
    $ns . '#GetAppointments',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Remove an Appointment'        // documentation
);

/* Register the method to return a single ComplexType1 */
$server->register('InsertGroupAppointment',                    // method name
    array('group' => 'xsd:string', 'date' => 'xsd:date', 'beg_time' => 'xsd:time', 'end_time' => 'xsd:time', 'type' => 'xsd:string'),   // input parameters
    array('return' => 'xsd:boolean'),    // output parameters
    $ns,                         // namespace
    $ns . '#InsertAppointment',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Insert a Common Appointment'        // documentation
);

/* Register the method to return a single ComplexType1 */
$server->register('RemoveGroupAppointment',                    // method name
    array('group' => 'xsd:string', 'date' => 'xsd:date', 'beg_time' => 'xsd:time'),   // input parameters
    array('return' => 'xsd:boolean'),    // output parameters
    $ns,                         // namespace
    $ns . '#RemoveAppointment',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Remove an Appointment'        // documentation
);

/* Register the method to return a single ComplexType1 */
$server->register('InsertGroupWeeklyAppointment',                    // method name
    array('group' => 'xsd:string', 'day' => 'xsd:string', 'beg_time' => 'xsd:time', 'end_time' => 'xsd:time', 'type' => 'xsd:string'),   // input parameters
    array('return' => 'xsd:boolean'),    // output parameters
    $ns,                         // namespace
    $ns . '#GetInsertWeeklyAppointment',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Insert a Weekly Appointment'        // documentation
);

/* Register the method to return a single ComplexType1 */
$server->register('RemoveGroupWeeklyAppointment',                    // method name
    array('group' => 'xsd:string', 'day' => 'xsd:date', 'beg_time' => 'xsd:time'),   // input parameters
    array('return' => 'xsd:boolean'),    // output parameters
    $ns,                         // namespace
    $ns . '#GetAppointments',                   // soapaction
    'rpc',                                    // style
    'encoded',                                // use
    'Remove an Appointment'        // documentation
);

// Register the method to return all ComplexType1s
$server->register('GetAllGroups',                    // method name
    array(),          // input parameters
    array('return' => 'tns:schedule'));    // output parameters
//    $ns,                         // namespace
//    $ns . '#GetAllGroups',                   // soapaction
//    'rpc',                                    // style
//    'encoded',                                // use
//    'Get All Groups'        // documentation
//);

?>
