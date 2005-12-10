<?php
if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
}

$master_session = Get_Crypt_Master_Session($sess_id, $bd);

Get_Account_Id($sess_id, $account_id, $bd);

$result_xsl = "xsl/" . $cfg['default_xsl'] . "/help.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchedErrors", $error);
$smarty->assign("nrpSchedAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpMasterSession", $master_session);

$result_xml = $smarty->fetch("xml/help.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>