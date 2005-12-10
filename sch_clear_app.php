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

$master_session = Get_Master_Session($sess_id, $bd);

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{

	Get_Account_Id($sess_id, $account_id, $bd);
	if (!empty($_POST['submit_conf_yes']) || !empty($_POST['submit_conf_no']))
	// If the submit button to confirm the appointment deletion was pressed
	{
		if (!empty($_POST['submit_conf_yes']))
		{

			Clear_Schedule($account_id, $bd);
			$alert[$num_alerts++] = 'Schedule cleared successfully';
		}
		else
		{
			$alert[$num_alerts++] = 'Schedule cleaning cancelled';
		}
		include "scheduling.php";
		exit;
	}
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$result_xsl = "xsl/" . $cfg['default_xsl'] . "/sch_clear_app.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchErrors", $error);
$smarty->assign("nrpSchAlerts", $alert);
$smarty->assign("nrpMasterSessId", $master_session);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpUserId", $account_id);

$result_xml = $smarty->fetch("xml/scheduling.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
