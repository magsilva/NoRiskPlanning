<?php
if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
	require_once("./inc/nrp_api.php");
}

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);

	$groups = List_Groups_Member($account_id, 0, $bd);
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$result_xsl = "xsl/" . $cfg['default_xsl'] . "/groups.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchedErrors", $error);
$smarty->assign("nrpSchedAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpGroups", $groups);
$smarty->assign("nrpUserId", $account_id);

$result_xml = $smarty->fetch("xml/groups.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
