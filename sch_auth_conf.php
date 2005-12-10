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

$master_session = Get_Crypt_Master_Session($sess_id, $bd);
$master_session_id = Get_Master_Session($sess_id, $bd);
if (!$master_session_id == 0)
	Get_Account_Id($master_session, $owner, $bd);
else
	$owner = '';

if (!empty($_GET['perio']))
	$perio = $_GET['perio'];
else
	$perio = $_POST['perio'];

if (!empty($_GET['app_id']))
	$app_id = $_GET['app_id'];
else
	$app_id = $_POST['app_id'];
	
if (!empty($_POST['conf']))
	$conf = $_POST['conf'];
else
	$conf = $_GET['conf'];

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	if (!empty($_GET['app_id']) || !empty($_POST['app_id']))
	{
		$_POST['app_id'] = $_GET['app_id'];
	}
	else
	{
		$error[$num_errors++] = "Invalid appointment ID";
		include "sch_auth.php";
		exit;
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

	if ($perio == 'common')
		$app = List_Appointments($account_id, '', '', '', '', '', '', '', $app_id, '',
			$cfg['time'], $array_type, $array_color, $array_image, $cfg['days'], $bd);
	else
		$app = List_Weekly_Appointments($account_id, '', '', '', '', '', '', '', $app_id, '',
			$cfg['time'], $array_type, $array_color, $array_image, $cfg['days'], $bd);

	if (!empty($_POST['submit_conf_yes']))
	// If the authorization was confirmed
	{
		if ($app[0][1] == 'weekly')
		{
			$overlap_apps = Check_Weekly_Appointment_Overlap($account_id, $app[0][15],
				$app[0][7], $app[0][9], '', $bd);
			if ($overlap_apps)
			{
				for ($i = 0; $i < $bd->NumberOfRows($overlap_apps); $i++)
				{
					$overlap_app_id = $bd->FetchResult($overlap_apps, $i, 'app_id');
					Delete_Weekly_Appointment($account_id, $overlap_app_id, $bd);
				}
			}
			if ($conf == 'yes')
			{
				$update = "UPDATE weekly_appointments SET authorized = 1 WHERE
					app_id = " . $app[0][0];
				$alert[$num_alerts++] = "Appointment authorized successfully";
			}
			else if ($conf == 'no')
			{
				$update = "DELETE FROM weekly_appointments WHERE app_id = " . $app[0][0];
				$alert[$num_alerts++] = "Unauthorized Appointment removed successfully";
			}
			$bd->Query($update);

			include "sch_auth.php";
			exit;
		}
		else
		{
			$overlap_apps = Check_Appointment_Overlap($account_id, $app[0][4], $app[0][5], $app[0][6],
				$app[0][7], $app[0][9], '', $bd);
			if ($overlap_apps)
			{
				for ($i = 0; $i < $bd->NumberOfRows($overlap_apps); $i++)
				{
					$overlap_app_id = $bd->FetchResult($overlap_apps, $i, 'app_id');
					Delete_Appointment($account_id, $overlap_app_id, $bd);
				}
			}
			
			if ($conf == 'yes')
			{
				$update = "UPDATE appointments SET authorized = 1 WHERE
					app_id = " . $app[0][0];
				$alert[$num_alerts++] = "Appointment authorized successfully.";
			}
			else
			{
				$update = "DELETE FROM appointments WHERE app_id = " . $app[0][0];
				$alert[$num_alerts++] = "Unauthorizied Appointment removed successfully.";
			}
			$bd->Query($update);
	
			include "sch_auth.php";
			exit;
		}
	}
	elseif (!empty($_POST['submit_conf_no']))
	// If the authoriation was cancelled
	{
		if ($conf == 'yes')
			$alert[$num_alerts++] = "Appointment authorization cancelled.";
		else if ($conf == 'no')
			$alert[$num_alerts++] = "Unauthorized Appointment removing cancelled.";
		include "sch_auth.php";
		exit;
	}
	else
	// If there is no confirmation
	{
		if ($conf == 'yes')
		{
			$apps = array();
			$overlap_apps = Check_Weekly_Appointment_Overlap($account_id, $app[0][15],
				$app[0][7], $app[0][9], '', $bd);
			echo $app[0][15];
	
	
			if ($overlap_apps)
			{
				for ($i = 0; $i < $bd->NumberOfRows($overlap_apps); $i++)
				{
					$cur_app = $bd->FetchResult($overlap_apps, $i, 'app_id');
					$res_app = List_Weekly_Appointments($account_id, '', '', 1, '', '', '', '',
						$cur_app, '', $cfg['time'], $array_type, $array_color,
						$array_image, $cfg['days'], $bd);
					$apps = array_merge($apps, $res_app);
				}
			}
			if ($perio == 'common')
			{
				$overlap_apps = Check_Appointment_Overlap($account_id, $app[0][4], $app[0][5], $app[0][6],
					$app[0][7], $app[0][9], '', $bd);
				if ($overlap_apps)
				{
					for ($i = 0; $i < $bd->NumberOfRows($overlap_apps); $i++)
					{
						$cur_app = $bd->FetchResult($overlap_apps, $i, 'app_id');
						$res_app = List_Appointments($account_id, '', '', 1, '', '',
							'', '', $cur_app, '', $cfg['time'],$array_type, $array_color,
							$array_image, $cfg['days'], $bd);
						$apps = array_merge($apps, $res_app);
					}
				}
			}
	
			if (count($apps) == 0)
			{
				$update = "UPDATE appointments SET authorized = 1 WHERE
					app_id = " . $app[0][0];
				$bd->Query($update);
				$alert[$num_alerts++] = "Appointment authorized successfully.";
				include "sch_auth.php";
				exit;
			}
			$result_xsl = "xsl/" . $default_xsl . "/sch_auth_conf.xsl";
		}
		else
		{
			$result_xsl = "xsl/" . $default_xsl . "/sch_auth_conf_no.xsl";
		}
		$apps = array_merge($apps, $app);
		$apps[0] = $apps;
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
$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpMainApp", $apps);
$smarty->assign("nrpMasterSession", $master_session);
$smarty->assign("nrpGroups", $groups);

$result_xml = $smarty->fetch("xml/groups_sched.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
