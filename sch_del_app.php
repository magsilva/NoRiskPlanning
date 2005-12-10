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
if (!$master_session == 0)
	Get_Account_Id($master_session, $owner, $bd);
else
	$owner = '';
	
$master_session = Get_Crypt_Master_Session($sess_id, $bd);

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{

	if ($_GET['is_pop'] || $_POST['is_pop'])
		$is_pop = 1;

	if (!empty($_GET['app_id']))
	{
		$_POST['app_id'] = $_GET['app_id'];
		$_POST['submit_choice'] = 'Remove';
	}
	
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

	if (!empty($_POST['submit_choice']))
	// If the submit button to choose the appointment was pressed
	{
		$apps =	List_Appointments($account_id, 0, '' , '' , '', '', '', '', $_POST['app_id'], '', $cfg['time'],
		      $array_type, $array_color, $array_image, $cfg['days'], $bd);
		if (!$is_pop)
			$result_xsl = "xsl/" . $default_xsl . "/sch_del_app1.xsl";
		else
			$result_xsl = "xsl/" . $default_xsl . "/sch_del_app_pop1.xsl";
			
	}
	else if (!empty($_POST['submit_conf_yes']) || !empty($_POST['submit_conf_no']))
	// If the submit button to confirm the appointment deletion was pressed
	{
		if (!empty($_POST['submit_conf_yes']))
		{

			Delete_Appointment($account_id, $_POST['app_id'], $bd);
			$alert[$num_alerts++] = 'Appointment deleted successfully';
			if (!empty($_POST['master_del']))
			{
				// Find similar owner's appointments to be deleted
				$apps =	List_Appointments($account_id, 0, '' , '', '', '' , '', '', $_POST['app_id'], '',
					$cfg['time'], $array_type, $array_color, $array_image, $cfg['days'], $bd);
				$date = $apps[0][6] . "-" .$apps[0][5] . "-" . $apps[0][4];
				$apps1 = List_Appointments(0, $owner, '', '', $date, $date, $apps[0][7],
					$apps[0][9], '', '', $cfg['time'], $array_type, $array_color, $array_time,
					$cfg['days'], $bd);
				if (!empty($apps1))
				{
					Delete_Appointment($owner, $apps1[0][0], $bd);
					$alerts[$num_alerts++] = "Appointment in Owner Schedule Deleted";
				}
			}
		}
		else
		{
			$alert[$num_alerts++] = 'Appointment deletion cancelled';
		}
		if (!$is_pop)
		{
			include "scheduling.php";
			exit;
		}
		else
			$result_xsl = "xsl/" . $default_xsl . "/sch_pop_final.xsl";
	}
	else
	// If the appointment to be deleted has to be chosen still
	{
		$apps =	List_Appointments($account_id, 0, '' , '' , '', '', '', '', '', '', $cfg['time'],
		      $array_type, $array_color, $array_image, $cfg['days'], $bd);
		$result_xsl = "xsl/" . $default_xsl . "/sch_del_app.xsl";
	}
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
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpSchSpan", 'all');
$smarty->assign("nrpGroupId", '');
$smarty->assign("nrpGroup", '');
$smarty->assign("nrpMainApp", $apps);
$smarty->assign("nrpMasterSession", $master_session);

$result_xml = $smarty->fetch("xml/sch_del_app.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
