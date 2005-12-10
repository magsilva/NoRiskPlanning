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

$m_session = Get_Master_Session($sess_id, $bd);
if (!$m_session == 0)
	Get_Account_Id($m_session, $owner, $bd);
else
	$owner = '';

$master_session = Get_Crypt_Master_Session($sess_id, $bd);

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);
	$query = "SELECT * FROM accounts WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	$role = $bd->FetchResult($result, 0, 'role');
	$var_type = $role . '_type';
	$var_color = $role . '_color';
	$var_image = $role . '_icon';
	$array_type = $cfg[$var_type];
	$array_color = $cfg[$var_color];
	$array_image = $cfg[$var_image];

	$apps =	List_Appointments($account_id, 0, '' , '', '', '' , '', '', '', $owner, $cfg['time'], $array_type,
		$array_color, $array_image, $cfg['days'], $bd);
	$result_xsl = "xsl/" . $default_xsl . "/sch_mod_app.xsl";
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchErrors", $error);
$smarty->assign("nrpSchAlerts", $alert);
$smarty->assign("nrpMasterSession", $master_session);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpSchSpan", 'all');
$smarty->assign("nrpMainApp", $apps);

$result_xml = $smarty->fetch("xml/sch_del_app.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
