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

	if (!empty($_GET['is_pop']))
	{
		$_POST['beg_time'] = $_GET['beg_time'];
		$_POST['end_time'] = $_GET['end_time'];
		$_POST['day'] = $_GET['day'];
		$_POST['month'] = $_GET['month'];
		$_POST['year'] = $_GET['year'];
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
		$result_xsl = "xsl/" . $default_xsl . "/groups_sch_ins_app_pop.xsl";
	else
		$result_xsl = "xsl/" . $default_xsl . "/groups_sch_ins_app.xsl";

	if (!empty($_POST['submit_ins']))
	// If the submit button to insert the appointment was pressed
	{
		if ( User_Validate_Simple_Field($_POST['description'], 100) <= 0)
			$error[$num_errors++] = "The appointment description must be informed";
		if ( $_POST['beg_time'] >= $_POST['end_time'])
			$error[$num_errors++] = "The ending time must be greater than the beginning time";
		if ( (User_Validate_Simple_Field($_POST['day'], 2) <= 0) && is_int($_POST['day']))
			$error[$num_errors++] = "The day must be informed correctly";
		if ( (User_Validate_Simple_Field($_POST['year'], 2) <= 0) && is_int($_POST['year']))
			$error[$num_errors++] = "The year must be informed correctly";
		else
		{
			$max_day = date('t', mktime(0,0,0, $_POST['month'], 1, $_POST['year']));
			if ($_POST['day'] > $max_day)
				$error[$num_errors++] = "Invalid day";
		}

		$check = Group_Check_Appointment_Overlap($group_id, $_POST['day'], $_POST['month'],
			$_POST['year'], $_POST['beg_time'], $_POST['end_time'], $bd);

		if ($check)
		{
			$error[$num_errors++] = "One of the members has another appointment within this time span ";
		}

		if (empty($error))
		{
			$day_w = date('w', mktime(0, 0, 0, $_POST['month'], $_POST['day'], $_POST['year']));
			$check  = Group_Check_Weekly_Appointment_Overlap($group_id, $day_w,
				$_POST['beg_time'], $_POST['end_time'], $bd);

			if (!$check)
			// If there aren't weekly appointments
			{
				Group_Insert_Appointment($group_id, $_POST['description'], $_POST['type'],
					$_POST['year']."-".$_POST['month']."-".$_POST['day'], $_POST['beg_time'],
					$_POST['end_time'], $_POST['url'], $account_id,  $bd);
				$alert[$num_alerts++] = "Appointment Inserted Successfully";

				if (!$is_pop)
				{
					include "groups_schedule.php";
					exit;
				}
				else
				{
					$result_xsl = "xsl/" . $default_xsl . "/sch_pop_final.xsl";
				}
			}
			else
			{
				if ($is_pop)
					$result_xsl = "xsl/" . $default_xsl . "/groups_sch_ins_app_pop1.xsl";
				else
					$result_xsl = "xsl/" . $default_xsl . "/groups_sch_ins_app1.xsl";
			}
		}
	}
	elseif (!empty($_POST['submit_conf_yes']) || !empty($_POST['submit_conf_no']))
	{
		if (!empty($_POST['submit_conf_yes']))
		{
			Group_Insert_Appointment($group_id, $_POST['description'], $_POST['type'],
				$_POST['year']."-".$_POST['month']."-".$_POST['day'], $_POST['beg_time'],
				$_POST['end_time'], $_POST['url'], $account_id, $bd);
			$alert[$num_alerts++] = "Appointment Inserted Successfully";
		}
		else
		{
			$alert[$num_alerts++] = 'Appointment Insertion Cancelled';
		}
		if (!$is_pop)
		{
			include "groups_schedule.php";
			exit;
		}
		else
		{
			$result_xsl = "xsl/" . $default_xsl . "/sch_pop_final.xsl";
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
$month = $_POST['month'];
$year = $_POST['year'];

$group = List_Groups($group_id, '', '', '', 1, $bd);

if (empty($submit_ins) && empty($submit_conf_yes) && empty($submit_conf_no) && !($is_pop))
// If the form is being opened for the first time
{
	$day = date('d');
	$month = date('m');
	$year = date('Y');
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
$smarty->assign("nrpPeriodicity", 'common');
$smarty->assign("nrpDescription", $_POST['description']);
$smarty->assign("nrpDay", $day);
$smarty->assign("nrpMonth", $month);
$smarty->assign("nrpYear", $year);
$smarty->assign("nrpBegId", $beg_time);
$smarty->assign("nrpEndId", $end_time);
$smarty->assign("nrpBeg", $cfg['time'][$beg_time]);
$smarty->assign("nrpEnd", $cfg['time'][$end_time]);
$smarty->assign("nrpTypeId", $_POST['type']);
$smarty->assign("nrpType", $array_type[$_POST['type']]);
$smarty->assign("nrpUrl", $_POST['url']);
$smarty->assign("nrpOwner", $owner);
$smarty->assign("nrpGroupId", $group_id);
$smarty->assign("nrpGroup", $group[0][1]);
$smarty->assign("nrpBefore", '');
$smarty->assign("nrpAfter", '');
$smarty->assign("nrpRoom", $_POST['room']);
$smarty->assign("nrpSchInsMaster", $ins_at_master);
$smarty->assign("nrpSchMasterSessId", $master_session);
$smarty->assign("nrpSchMonths", $cfg['months']);

$result_xml = $smarty->fetch("xml/sch_ins_app.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
