<?php

if (!empty($_POST['switch_to_eventual']))
{
	$_POST['day'] = $_POST['day_common'];
	
	include "sch_ins_app.php";
	exit;
}

if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
}
require("./inc/nrp_api.php");

if (!isset($num_errors))
	$num_errors = 0;
if (!isset($num_alerts))
	$num_alerts = 0;

if (!empty($_POST['date_day']))
{
	$date_day = $_POST['date_day'];
	$date_month = $_POST['date_month'];
	$date_year = $_POST['date_year'];
}
else if (!empty($_GET['date_day']))
{
	$date_day = $_GET['date_day'];
	$date_month = $_GET['date_month'];
	$date_year = $_GET['date_year'];
}

$master_session = Get_Master_Session($sess_id, $bd);

if ($_POST['ins_at_master'] == 'on')
	$ins_at_master = 1;
else
	$ins_at_master = 0;
	
$master_session = Get_Crypt_Master_Session($sess_id, $bd);

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	if ($_GET['is_pop'] || $_POST['is_pop'])
		$is_pop = 1;

	if ($_GET['app_id'])
	{
		$_POST['app_id'] = $_GET['app_id'];
	}

	$app_id = $_POST['app_id'];

	Get_Account_Id($sess_id, $account_id, $bd);

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

	if (!$is_pop)
		$result_xsl = "xsl/" . $default_xsl . "/sch_mod_week_app1.xsl";
	else
		$result_xsl = "xsl/" . $default_xsl . "/sch_mod_week_app_pop1.xsl";

	if (!empty($_POST['submit_mod']))
	// If the submit button to insert the appointment was pressed
	{

		$description = $_POST['description'] ;
		$beg_time = $_POST['beg_time'];
		$end_time = $_POST['end_time'];
		$day = $_POST['day'];
		$url = $_POST['url'];
		$type = $_POST['type'];

		if ( User_Validate_Simple_Field($_POST['description'], 100) <= 0)
			$error[$num_errors++] = "The appointment description must be informed";
		if ( $_POST['beg_time'] >= $_POST['end_time'])
			$error[$num_errors++] = "The ending time must be greater than the beginning time";

		$check = Check_Weekly_Appointment_Overlap($account_id, $_POST['day'], $_POST['beg_time'],
			$_POST['end_time'], $app_id, $bd);
		if ($check)
			$error[$num_errors++] = "There is another weekly appointment within this time span";
		if ($ins_at_master)
		{
			$apps_bef = List_Weekly_Appointments($account_id, '', '', '' , '' , '',
				'', '', '', $app_id, '', $cfg['time'], $array_type,
				$array_color, $array_image, $cfg['days'], $bd);

			$apps =	List_Weekly_Appointments($owner, '', '', '', '',
				$apps_bef[0][15], $apps_bef[0][15] ,
				$apps_bef[0][7], $apps_bef[0][9], '', '', $cfg['time'],
				$array_type, $array_color,
			  	$array_image, $cfg['days'], $bd);
			if (!empty($apps))
			{
				$app_id_owner = $apps[0][0];
				$check = Check_Weekly_Appointments_Overlap($owner, $_POST['day'], $_POST['beg_time'],
					$_POST['end_time'], $app_id_owner, $bd);
					if ($check)
						$error[$num_errors++] = "There is another weekly appointment on the owner's
						 schedule within this time span";
			}
		}

		if (empty($error))
		{

			Update_Weekly_Appointment($app_id, $_POST['description'], $_POST['type'],
				$_POST['day'], $_POST['beg_time'], $_POST['end_time'], $_POST['url'], $bd);
			$alert[$num_alerts++] = "Appointment Modified Successfully";

			if ($ins_at_master)
			{
				Update_Weekly_Appointment($app_id_owner, $_POST['description'], $_POST['type'],
					$_POST['day'], $_POST['beg_time'], $_POST['end_time'], $_POST['url'], $bd);
				$alert[$num_alerts++] = "Appointment Modified Successfully at Owner's Schedule";
			}

			if (!$is_pop)
			{
				include "scheduling.php";
				exit;
			}
			else
				$result_xsl = "xsl/" . $default_xsl . "/sch_pop_final.xsl";
		}
	}
	else if (!empty($submit_choice) || $is_pop)
	// If the user has just chosen the appointment to be modified
	{
		$apps =	List_Weekly_Appointments($account_id, '', '', '' , '', '', '' , '',
			$app_id, '', $cfg['time'], $array_type,
			$array_color, $array_image, $cfg['days'], $bd);
		$description = $apps[0][2];
		$beg_time = $apps[0][7];
		$end_time = $apps[0][9];
		$day = $apps[0][15];
		$url = $apps[0][17];
		$type = $apps[0][11];
		$owner = $apps[0][18];
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
$smarty->assign("nrpSchTypeName", $array_type);
$smarty->assign("nrpSchTypeColor", $array_color);
$smarty->assign("nrpSchTypeImage", $array_image);
$smarty->assign("nrpSchTimes", $cfg['time']);
$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpAppId", $app_id);
$smarty->assign("nrpSchSpan", 'all');
$smarty->assign("nrpPeriodicity", 'week');
$smarty->assign("nrpDescription", $description);
$smarty->assign("nrpBegId", $beg_time);
$smarty->assign("nrpEndId", $end_time);
$smarty->assign("nrpBeg", $cfg['time'][$beg_time]);
$smarty->assign("nrpEnd", $cfg['time'][$end_time]);
$smarty->assign("nrpTypeId", $type);
$smarty->assign("nrpType", $array_type[$type]);
$smarty->assign("nrpUrl", $url);
$smarty->assign("nrpGroupId", '');
$smarty->assign("nrpGroup", '');
$smarty->assign("nrpDay", $date_day);
$smarty->assign("nrpMonth", $date_month);
$smarty->assign("nrpYear", $date_year);
$smarty->assign("nrpDayOfWeekId", $day);
$smarty->assign("nrpDayOfWeek", $cfg['days'][$day]);
$smarty->assign("nrpSchInsMaster", $ins_at_master);
$smarty->assign("nrpMasterSession", $master_session);
$smarty->assign("nrpSchDays", $cfg['days']);

$result_xml = $smarty->fetch("xml/sch_ins_week_app.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
