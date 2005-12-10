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
	Get_Account_Id($sess_id, $account_id, $bd);

	if ($_GET['is_pop'] || $_POST['is_pop'])
		$is_pop = 1;

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

	// If the user is actually owner of the group
	if (! ($membership == 'O' || $membership != 'M') )
	{
		$error[$num_errors++] = "You are not moderator of this group.";
		include "groups.php";
		exit;
	}

	if ($is_pop)
		$result_xsl = "xsl/" . $default_xsl . "/groups_sch_ins_week_app_pop.xsl";
	else
		$result_xsl = "xsl/" . $default_xsl . "/groups_sch_ins_week_app.xsl";

	if (!empty($_POST['submit_ins']))
	// If the submit button to insert the appointment was pressed
	{
		if ( User_Validate_Simple_Field($_POST['description'], 100) <= 0)
			$error[$num_errors++] = "The appointment description must be informed";
		if ( $_POST['beg_time'] >= $_POST['end_time'])
			$error[$num_errors++] = "The ending time must be greater than the beginning time";

		$check = Group_Check_Weekly_Appointment_Overlap($group_id, $_POST['day'], $_POST['beg_time'],
			$_POST['end_time'], $bd);
		if ($check)
			$error[$num_errors++] = "One of the members has another weekly appointment within this time span";

		if (empty($error))
		{
			Group_Insert_Weekly_Appointment($group_id, $_POST['description'], $_POST['type'],
				$_POST['day'], $_POST['beg_time'], $_POST['end_time'], $_POST['url'],
				$account_id, $bd);
			$alert[$num_alerts++] = "Appointment Inserted Successfully";

			if (!$is_pop)
			{
				include "groups_schedule.php";
				exit;
			}
			else
			{
				$result_xsl = "xsl/" . $default_xsl . "/groups_sch_pop_final.xsl";
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

$group = List_Groups($group_id, '', '', '', 0, $bd);

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
$smarty->assign("nrpOwner", $account_id);
$smarty->assign("nrpGroupId", $group_id);
$smarty->assign("nrpGroup", $group[0][1]);
$smarty->assign("nrpSchInsMaster", $ins_at_master);
$smarty->assign("nrpSchMasterSessId", $master_session);
$smarty->assign("nrpSchDays", $cfg['days']);

$result_xml = $smarty->fetch("xml/sch_ins_week_app.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
