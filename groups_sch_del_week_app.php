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

if (!empty($_GET['group_id']))
	$group_id = $_GET['group_id'];
else if (!empty($_POST['group_id']))
	$group_id = $_POST['group_id'];

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	if ($_GET['is_pop'] || $_POST['is_pop'])
		$is_pop = 1;
	
	if (!empty($_GET['app_id']))
		$_POST['app_id'] = $_GET['app_id'];
	if (!empty($_GET['app_id']))
		$_POST['submit_choice'] = 'submit';
	
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

	// If the user is actually owner of the group
	if (! ($membership == 'O' || $membership != 'M') )
	{
		$error[$num_errors++] = "You are not moderator of this group.";
		include "groups.php";
		exit;
	}

	if (!empty($_POST['submit_choice']))
	// If the submit button to choose the appointment was pressed
	{
		$apps = List_Weekly_Appointments($account_id, 1, $group_id, '', '', '', '', '', $_POST['app_id'], '',
		     $cfg['time'], $array_type, $array_color, $array_image, $cfg['days'], $bd);

		if (!$is_pop)
			$result_xsl = "xsl/" . $default_xsl . "/groups_sch_del_week_app1.xsl";
		else
			$result_xsl = "xsl/" . $default_xsl . "/groups_sch_del_week_app_pop1.xsl";
			
	}
	else if (!empty($_POST['submit_conf_yes']) || !empty($_POST['submit_conf_no']))
	// If the submit button to confirm the appointment deletion was pressed
	{
		if (!empty($_POST['submit_conf_yes']))
		{

			Group_Delete_Weekly_Appointment($_POST['app_id'], $account_id, $bd);
			$alert[$num_alerts++] = 'Appointment deleted successfully';
			if (!empty($_POST['master_del']))
			{
				$apps = List_Weekly_Appointments($account_id, 1, $group_id, '', '', '', '', '',
			       		$_POST['app_id'], '', $cfg['time'], $array_type, $array_color, 
					$array_image, $cfg['days'], $bd);
			}
		}
		else
		{
			$alert[$num_alerts++] = 'Appointment deletion cancelled';
		}
		if (!$is_pop)
		{
			include "groups_schedule.php";
			exit;
		}
		else
			$result_xsl = "xsl/" . $default_xsl . "/groups_sch_pop_final.xsl";
	}
	else
	// If the appointment to be deleted has to be chosen still
	{
		$apps = List_Weekly_Appointments($account_id, 1, $group_id, '', '', '', '', '', 
	       		$_POST['app_id'], $owner, $cfg['time'], $array_type, $array_color, 
			$array_image, $cfg['days'], $bd);

		$result_xsl = "xsl/" . $default_xsl . "/groups_sch_del_week_app.xsl";
	}
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$group = List_Groups($group_id, '', '', '', 0, $bd);

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpMasterSessId", $master_Session);
$smarty->assign("nrpSchErrors", $error);
$smarty->assign("nrpSchAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpSchSpan", 'all');
$smarty->assign("nrpGroupId", $group_id);
$smarty->assign("nrpGroup", $group[0][1]);
$smarty->assign("nrpMainApp", $apps);

$result_xml = $smarty->fetch("xml/sch_del_app.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
