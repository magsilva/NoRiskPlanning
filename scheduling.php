<?php
if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
}


if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	$master_session = Get_Crypt_Master_Session($sess_id, $bd);

	Get_Account_Id($sess_id, $account_id, $bd);
	
	$result_xsl = "xsl/" . $cfg['default_xsl'] . "/scheduling.xsl";
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}
	
$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchedErrors", $error);
$smarty->assign("nrpSchedAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpMasterSession", $master_session);
$smarty->assign("nrpUserId", $account_id);

$result_xml = $smarty->fetch("xml/scheduling.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
