<?php

/* In this files we put the implementation of support fucntions for the
 * web services implementations
 *
 * Contributors:
 *     Alexandre Michetti Manduca (a.michetti@gmail.com)
*/


// Require the necessary files
require_once("./inc/config.inc.php");
require_once("./inc/database_handler.php");
require_once("./inc/session_handler.php");
require_once("./inc/nrp_api.php");

// We do this so this variables can be used inside the service functions
$GLOBALS['bd'] = $bd;   // Data Base Handler  (see inc/database_handler.php)
$GLOBLAS['cfg'] = $cfg; // Configuration Hash (see inc/config.inc.php)

function time2nrptime($time)
{
    if ($time < $GLOBALS['cfg']['first_time']) return -1;
    if ($time > $GLOBALS['cfg']['last_time']) return -1;

    // The value "should" come in "HH:MM:SS"    
    $aux = split(":", $time);

    // If the minutes are higher than 30, increment the hours
    if ($aux[1] != null) {
        if ($aux[1] > 30) $aux[0]++;
    }

    // Convert to NRP time (see inc/config.inc.php)
    $nrp_time = $aux[0] - $GLOBALS['cfg']['first_time'];

    return $nrp_time;
}

function type2nrptype($type)
{
    $types = $GLOBALS['cfg']['user_type'];

    // Look for the type and returned the array index
    foreach ($types as $key => $value) {
        if ($type == $value) return $key;
    }
}

?>
