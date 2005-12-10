<?php
if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
}
require("./inc/nrp_api.php");

if (!isset($num_errors))
	$num_errors = 0;
if (!isset($num_alerts))
	$num_alerts = 0;

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);
	
	$master_session = Get_Crypt_Master_Session($sess_id, $bd);
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$result_xsl = "xsl/" . $cfg['default_xsl'] . "/sch_img.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchErrors", $error);
$smarty->assign("nrpSchAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpMasterSession", $master_session);
$smarty->assign("nrpPath", $cfg['url']);

$result_xml = $smarty->fetch("xml/sch_img.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
