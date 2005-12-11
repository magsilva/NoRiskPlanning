<?php

error_reporting(E_ALL);

require("./inc/config.inc.php");
require("./inc/database_handler.php");
require("./inc/xsl_handler.php");
require("./inc/session_handler.php");

// Start the template handling component
define("SMARTY_DIR","./smarty/");
require(SMARTY_DIR."Smarty.class.php");
$smarty = new Smarty;
$smarty->security = 1;
$smarty->secure_dir=array("./");
$smarty->compile_dir = "./smarty/template_c/";


// Prepare the session token
if (isset($_GET['sess_id']))
	$complete_sess_id = $_GET['sess_id'];
else if (isset($_POST['sess_id']))
	$complete_sess_id = $_POST['sess_id'];
else if (isset($sess_id))
	$complete_sess_id = "$sess_id";

$default_xsl = 'default';
if (isset($complete_sess_id))
{
	$sess_id = substr($complete_sess_id, 32);
	$default_xsl = Get_Exibition($sess_id, $bd);
}

// Initialize error variables
$error = array();
$alert = array();

$already_initialized = 1;
?>
