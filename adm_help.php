<?php
if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
	include "./inc/nrp_api.php";
}

if (!isset($num_errors))
	$num_errors = 0;
if (!isset($num_alerts))
	$num_alerts = 0;

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);
	if ($account_id != 'admin')
	{
		$error[$num_errors++] = "You are not the administrator";
		include "logout.php";
		exit;
	}
}
else
{
		$error[$num_errors++] = "Invalid Session ID";
		include "logout.php";
		exit;
}

$result_xsl = "xsl/" . $default_xsl . "/adm_help.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpSchedErrors", $error);
$smarty->assign("nrpSchedAlerts", $alert);

$result_xml = $smarty->fetch("xml/adm_help.xml");

require("./inc/proc_transform.php");
// Calls the commands to process the XSLT transformation
?>
