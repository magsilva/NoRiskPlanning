<?php

if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
	require_once("./inc/nrp_api.php");
}

$master_session = Get_Master_Session($sess_id, $bd);

if (!isset($num_errors))
	$num_errors = 0;
if (!isset($num_alerts))
	$num_alerts = 0;

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);

	$owner = $account_id;
	
	$rooms = User_List_Courses($account_id, $bd);
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$result_xsl = "xsl/" . $cfg['default_xsl'] . "/sch_courses.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchErrors", $error);
$smarty->assign("nrpSchAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpCourses", $rooms);
$smarty->assign("nrpUserId", $account_id);

$result_xml = $smarty->fetch("xml/adm_acc_courses.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>