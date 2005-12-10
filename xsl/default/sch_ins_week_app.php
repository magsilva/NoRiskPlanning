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

if ($_POST['ins_at_master'] == 'on')
	$ins_at_master = 1;
else
	$ins_at_master = 0;

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);

	if (!$_GET['is_pop]' || !$_POST['is_pop'])
		$is_pop = 1;

	if (!$master_session)
		$owner = $account_id;
	else
		Get_Account_Id($master_session, $owner, $bd);

	if (!empty($_POST['month']))
	{
		$day = date('w', mktime(1, 1, 1, $_POST['month'], $_POST['day'], $_POST['year']));
		$_POST['day'] = $day;
	}

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
		$result_xsl = "xsl/" . $default_xsl . "/sch_ins_week_app.xsl";
	else
		$result_xsl = "xsl/" . $default_xsl . "/sch_ins_week_app_pop.xsl";

	if (!empty($_POST['submit_ins']))
	// If the submit button to insert the appointment was pressed
	{
		if ( User_Validate_Simple_Field($_POST['description'], 100) <= 0)
			$error[$num_errors++] = "The appointment description must be informed";
		if ( $_POST['beg_time'] >= $_POST['end_time'])
			$error[$num_errors++] = "The ending time must be greater than the beginning time";

		$check = Check_Weekly_Appointment_Overlap($account_id, $_POST['day'], $_POST['beg_time'],
			$_POST['end_time'], '', $bd);
		if ($check)
			$error[$num_errors++] = "There is another weekly appointment within this time span";
		if ($ins_at_master)
		{
			$check = Check_Weekly_Appointments_Overlap($owner, $_POST['day'], $_POST['beg_time'],
				$_POST['end_time'], '', $bd);
				if ($check)
					$error[$num_errors++] = "There is another weekly appointment on the owner's schedule within this time span";
		}

		if (empty($error))
		{
			Insert_Weekly_Appointment($account_id, $_POST['description'], $_POST['type'], $_POST['day'],
				$_POST['beg_time'], $_POST['end_time'], $_POST['url'], $owner, 0, 0, 1,
				$_POST['$room_id'], $bd);
			$alert[$num_alerts++] = "Appointment Inserted Successfully";

			if ($ins_at_master)
			{
				Insert_Weekly_Appointment($owner, $_POST['description'], $_POST['type'], $_POST['day'],
					$_POST['beg_time'], $_POST['end_time'], $_POST['url'], $owner, 0, 0, 1,
					$_POST['$room_id'], $bd);
				$alert[$num_alerts++] = "Appointment Inserted Successfully at Owner's Schedule";
			}

			if (!$is_pop)
			{
				include "scheduling.php";
				exit;
			}
			else
			{
				$result_xsl = "xsl/" . $default_xsl . "/sch_pop_final.xsl";
			}
		}
	}
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$beg_time = $_POST['beg_time'];
$end_time = $_POST['end_time'];

$day = $_POST['day'];

if (empty($submit_ins) && !$is_pop)
// If the form is being opened for the first time
{
	$day = date('w');
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
$smarty->assign("nrpSchSpan", 'all');
$smarty->assign("nrpPeriodicity", 'week');
$smarty->assign("nrpDescription", $_POST['description']);
$smarty->assign("nrpBegId", $beg_time);
$smarty->assign("nrpEndId", $end_time);
$smarty->assign("nrpBeg", $cfg['time'][$beg_time]);
$smarty->assign("nrpEnd", $cfg['time'][$end_time]);
$smarty->assign("nrpType", $_POST['type']);
$smarty->assign("nrpDayOfWeekId", $day);
$smarty->assign("nrpUrl", $_POST['url']);
$smarty->assign("nrpOwner", $owner);
$smarty->assign("nrpSchInsMaster", $ins_at_master);
$smarty->assign("nrpSchMasterSessId", $master_session);
$smarty->assign("nrpSchDays", $cfg['days']);

$result_xml = $smarty->fetch("xml/sch_ins_week_app.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
