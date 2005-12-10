<?php
if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
	require_once("./inc/nrp_api.php");
}

if (!isset($num_errors))
	$num_errors = 0;
if (!isset($num_alerts))
	$num_alerts = 0;

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);
	
	$master_session = Get_Crypt_Master_Session($sess_id, $bd);

	if (!$master_session)
		$owner = $account_id;
	else
		Get_Account_Id($master_session, $owner, $bd);

	$query = "SELECT * FROM accounts WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	$role = $bd->FetchResult($result, 0, 'role');
	$var_type = $role . '_type';
	$var_color = $role . '_color';
	$var_image = $role . '_icon';
	$array_type = $cfg[$var_type];
	$array_color = $cfg[$var_color];
	$array_image = $cfg[$var_image];

	if (!empty($_POST['search']))
	{
		$apps1 = Search_Appointments($account_id, $_POST['key'], $cfg['time'], $array_type,
			$array_color, $array_image, $cfg['days'], $bd);
		$apps2 = Search_Weekly_Appointments($account_id, $_POST['key'], $cfg['time'], $array_type,
			$array_color, $array_image, $cfg['days'], $bd);
		$apps = array_merge($apps1, $apps2);
		$result_xsl = "xsl/" . $cfg['default_xsl'] . "/search_res.xsl";
	}
	else
	{
		$result_xsl = "xsl/" . $cfg['default_xsl'] . "/search.xsl";
	}
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpMainErrors", $error);
$smarty->assign("nrpMainAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpMainApp", $apps);
$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpMasterSession", $master_session);

$result_xml = $smarty->fetch("xml/main.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
