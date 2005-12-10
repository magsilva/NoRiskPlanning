<?php
require("./inc/config.inc.php");
require("./inc/database_handler.php");
require("./inc/xsl_handler.php");
require("./inc/session_handler.php");

define("SMARTY_DIR","./smarty/");
require(SMARTY_DIR."Smarty.class.php");
$smarty = new Smarty;
$smarty->security = 1;
$smarty->secure_dir=array("./");
$smarty->compile_dir = "./smarty/template_c/";
// Starts the template handling component

if (!empty($_GET['sess_id']))
	$complete_sess_id = $_GET['sess_id'];
else if (!empty($_POST['sess_id']))
	$complete_sess_id = $_POST['sess_id'];
else
	$complete_sess_id = $sess_id;

if (!empty($sess_id))
{
	$complete_sess_id = "$complete_sess_id";
	$sess_id = substr($complete_sess_id, 32);
}

$default_xsl = Get_Exibition($sess_id, $bd);

if (empty($default_xsl))
	$default_xsl = 'default';

$already_initialized = 1;
// Sets if this script was already run
?>
