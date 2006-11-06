<?php

/* In this files we put the implementation of each functionality (tasks)
 * of the NoRisk Web Service.
 *
 * Contributors:
 *     Alexandre Michetti Manduca (a.michetti@gmail.com)
*/


/* Include the files which functions and data we will need on all
 * (or on the majority) of the functions (functionalities of the
 * service). If you need something that will be used just inside
 * a few functions, include/require it locally.
*/
require_once("./inc/config.inc.php");
require_once("./inc/database_handler.php");
require_once("./inc/session_handler.php");
require_once("./inc/nrp_api.php");
require_once("./services_utils.php");

// We do this so this variables can be used inside the service functions
$GLOBALS['bd'] = $bd;   // Data Base Handler  (see inc/database_handler.php)
$GLOBLAS['cfg'] = $cfg; // Configuration Hash (see inc/config.inc.php)


/* If the functionality you want to use require that you log in, use
 * this one first to get create a session.
 * The session number returned will be used as the first 'parameter' on the
 * other functionalities.
 *
 * Parameters:
 *    $account_id: The user id (string)
 *    $password: The user password (string)
 *
 * By: Alexandre Michetti Manduca (a.michetti@gmail.com)
*/
function login($account_id, $password)
{
    // Verify user data
    $auth_result = User_Authenticate_Password($account_id,
                                              $password,
                                              $GLOBALS['bd']);
   
    // Return erros if it found any problem
    if ($auth_result == 1) {
        $user_ok = 1;

    } elseif ($auth_result == 0) {
        return "Wrong Password";
    } else {
        return "User Not Found";
    }
   
    // Create the session code
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $sess_id = Create_Session($account_id, 'default', $ip_address, 1, 0, $GLOBALS['bd']);
    $crypt_sess_id = md5($sess_id);
    $complete_sess_id = $crypt_sess_id . $sess_id;

    // Return the session code to the user
    return $complete_sess_id;
}

/* Log Out from the No Risk Web Service. (finishes the session opened with
 * login)
 *
 * Parameters:
 *     $complete_sess_id: The session code returned by login
 *
 * By: Alexandre Michetti Manduca (a.michetti@gmail.com)
*/
function logout($complete_sess_id)
{
    // Validate the user session
    if (!Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $GLOBALS['bd'])) {
         return "Invalid session ID!";
    }
   
    // Finishe the session
    $sess_id = substr($complete_sess_id, 32);
    if (Terminate_Session($sess_id, 'logout', $GLOBALS['bd'])) {
        return "System logout successful!";
    }
}

/* Create a new appointment.
 * note: To create weekly appointments, use the newWeeklyAppointment
 *       functionality
 *
 * Parameters:
 *     $complete_sess_id: The session code returned by login (string)
 *     $description: Description of the new appointment
 *     $type: Type of the appointment (see inc/config.inc.php)
 *     $date: Date of the appointment
 *     $beg_time: Begining time of the appointment
 *     $end_time: End time of the appointment
 *     $url: Some URL relevant to the appointment
 *     
 * By: Alexandre Michetti Manduca (a.michetti@gmail.com)
*/
function newAppointment($complete_sess_id, $description, $type, $date,
                        $beg_time, $end_time, $url)
{
    if (!Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $GLOBALS['bd'])) {
         return "Invalid session ID!";
    }
    
    // Get the user account ID
    $sess_id = substr($complete_sess_id, 32);
    Get_Account_Id($sess_id, $account_id, $GLOBALS['bd']);

    // Convert the time values
    $nrp_beg_time = time2nrptime($beg_time);
    $nrp_end_time = time2nrptime($end_time);

    // Convert the type
    $nrp_type = type2nrptype($type);
   
    // Insert it
    Insert_Appointment($account_id, $description, $nrp_type, $date,
                       $nrp_beg_time, $nrp_end_time, $url, $account_id, 0, 0,
                       1, $GLOBALS['bd']);

    return 1;
}

/* Delete an appointment.
 * note: To delete an weekly appointment, use the delWeeklyAppointment
 *       functionality
 *
 * Parameters:
 *     $complete_sess_id: the session code returned by login
 *     $app_id: the appointment identification number
 *
 * By: XXX Tati
 *     Alexandre Michetti Manduca (a.michetti@gmail.com)
*/
function delAppointment($complete_sess_id, $app_id)
{
    if (!Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $GLOBALS['bd'])) {
         return "Invalid session ID!";
    }
    
    // Get the user account ID
    $sess_id = substr($complete_sess_id, 32);
    Get_Account_Id($sess_id, $account_id, $GLOBALS['bd']);

    // Delete it
    Delete_Appointment($account_id, $app_id, $GLOBALS['bd']);

    return 1;
}

/* Updates an appointment.
 * note: To update an weekly appointment, use the updateWeeklyAppointment
 *       functionality
 *
 * Parameters:
 *     $complete_sess_id: The session code returned by login (string)
 *     $app_id: the appointment identification number
 *     $description: Description of the new appointment
 *     $type: Type of the appointment (see inc/config.inc.php)
 *     $date: Date of the appointment
 *     $beg_time: Begining time of the appointment
 *     $end_time: End time of the appointment
 *     $url: Some URL relevant to the appointment
 *
 * By: XXX Tati
 *     Alexandre Michetti Manduca (a.michetti@gmail.com)
*/
function updateAppointment($complete_sess_id, $app_id, $description, $type, $date,
                     $beg_time, $end_time, $url)
{
    if (!Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $GLOBALS['bd'])) {
         return "Invalid session ID!";
    }
    
    // Convert the time values
    $nrp_beg_time = time2nrptime($beg_time);
    $nrp_end_time = time2nrptime($end_time);

    // Convert the type
    $nrp_type = type2nrptype($type);
   
    // Update it
    Update_Appointment($app_id, $description, $nrp_type, $date,
                       $nrp_beg_time, $nrp_end_time, $url, $GLOBALS['bd']);

    return 1;
}

/* Inserts a new person
 *
 * Parameters:
 *      $complete_sess_id: The session code returned by login (string)
 *      $account_id: String that identify the user
 *      $name: User name
 *      $comments: Comments about the user
 *      $email: User email
 *      $dep_id: Department identification number
 *      $url: Optional value for a URL
 *      $password: User password
 *      $category: User category
 * 
 * By: Filipe D. N. Grillo (filipe.grillo@gmail.com)
*/
function newPerson($complete_sess_id, $account_id, $name, $comments, $email, $dep_id,
                   $url, $password, $category)
{
    if (!Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $GLOBALS['bd'])) {
         return "Invalid session ID!";
    }
   
    /* XXX There must have one '0' for each public type defined on config.inc.php */ 
    Insert_Person($account_id, $name, $comments, $email, $dep_id,
                  $url, $password, $category, 0000, $GLOBALS['bd']);

    return 1;
}



/* Deletes a given user, and all its dependencies
 *
 *parameters:
 *      $complete_sess_id: The session code returned by login (string)
 *      $account_id: String that identify the user
 *
 *
 * By: Filipe D. N. Grillo (filipe.grillo@gmail.com)
*/
function deletePerson($complete_sess_id, $account_id)
{
    if (!Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $GLOBALS['bd'])) {
         return "Invalid session ID!";
    }

    return Delete_Person($account_id, $GLOBALS['bd']);
}



/* List the appointments of an user.
 * nnote1: Appart from $complete_sess_id, all the arguments are optional
 *        and should be used to filter the results.
 * note2: To get the list of an user weekly appointments, use the
 *        listWeeklyAppointments functionality
 *
 * Parameters:
 *     $complete_sess_id: The session code returned by login (string)
 *     $beg_day: The begining day of the search (string) - optional
 *     $end_day: The end day of the search (string) - optional
 *     $beg_time: The begining time of the search (string) - optional
 *     $end_time: The end time of the search (string) - optional
 *     $type: The type of the appointments to search for (string) - optional
 *     >>  values: see inc/config.ing.php
 *      
 * By: Alexandre Michetti Manduca (a.michetti@gmail.com)
 *
*/
function listAppointments($complete_sess_id, $beg_date, $end_date, $beg_time,
                          $end_time, $type)
{
    // Validate user session code
    if (!Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $GLOBALS['bd'])) {
         return "Invalid session ID!";
    }

    // Get the user account ID
    $sess_id = substr($complete_sess_id, 32);
    Get_Account_Id($sess_id, $account_id, $GLOBALS['bd']);

    // Get information about appointments types available
    $query = "SELECT * FROM accounts WHERE account_id = '$account_id'";
    $result = $GLOBALS['bd']->Query($query);
    $role = $GLOBALS['bd']->FetchResult($result, 0, 'role');
    $var_type = $role . '_type';

    // Set app types, times and days of week (see inc/config.inc.php)
    $app_times = $GLOBALS['cfg']['time'];
    $app_types = $GLOBALS['cfg'][$var_type];
    $days_of_week = $GLOBALS['cfg']['days'];

    // Convert $beg_time and $end_time to NoRisk values
    // see (inc/config.inc.php to see how norisk store this values
    // XXX Check if value is between first_time and last_time?
    $aux = split(":", $beg_time);
    $nrp_beg_time = $aux[0] - $GLOBALS['cfg']['first_time'];
    
    $aux = split(":", $end_time);
    $nrp_end_time = $aux[0] - $GLOBALS['cfg']['first_time'];

    // Get the list of appointments for this user
    // (does not include the weekly ones)
    $apps = List_Appointments($account_id, '', '', '', $beg_date, $end_date,
                              $nrp_beg_time, $nrp_end_time, '', '', $app_times,
                              $app_types, '', '', $days_of_week,
                              $GLOBALS['bd']);
   
    // If there are no results, return an empty array instead of a null
    if ($apps == null) return array();

    // Make the returning array
    $apps_list = array();
    foreach($apps as $app) {
        $aux = array(
            'app_id' => $app['0'],
            'description' => $app['2'],
            'beg_time' => $app['8'],
            'end_time' => $app['10'],
            'date' => $app['16'],
            'type' => $app['12'],
            'url' => $app['17'],
            'owner' => $app['18'],
        );

        array_push($apps_list, $aux);
    }
    
    // Return the appotinments list
    return $apps_list;
}

?>
