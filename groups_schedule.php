<?php
if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
	include "./inc/nrp_api.php";
}

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

	$membership = Member_Role($group_id, $account_id, $bd);

	// If the user is actually owner of the group
	if (! ($membership == 'O' || $membership != 'M') )
	{
		$error[$num_errors++] = "You are not moderator of this group.";
		include "groups.php";
		exit;
	}

	if (!empty($_POST['span_day']))
		$_POST['span'] = 'day';
	else if (!empty($_POST['span_week']))
		$_POST['span'] = 'week';
	else if (!empty($_POST['span_month']))
		$_POST['span'] = 'month';

	if (!empty($_POST['span']))
		$span = $_POST['span'];
	else
		$span = $cfg['default_schedule'];

	$span_start_day = $_POST['span_start_day'];
	$span_start_month = $_POST['span_start_month'];
	$span_start_year = $_POST['span_start_year'];
	$span_end_day = $_POST['span_end_day'];
	$span_end_month = $_POST['span_end_month'];
	$span_end_year = $_POST['span_end_year'];

	$mult_factor = 0;
	// Sets the multiplication factor to increment/decrement the
	// schedule span

	if (!empty($_POST['inc_span']))
	// If the 'next' button was pressed
	{
		$mult_factor = 1;
		$changed = 1;
	}
	else if (!empty($_POST['dec_span']))
	// If the 'previous' button was pressed
	{
		$mult_factor = -1;
		$changed = 1;
	}
	else if (!empty($_POST['current']))
		$changed = 0;

	if (!empty($_POST['span_day']))
	// If the span was set through a POST operation
	{
		$span = 'day';
		$changed = 1;
	}
	elseif (!empty($_POST['span_week']))
	{
		$span = 'week';
		$changed = 1;
	}
	elseif (!$changed)
	// If the span is blank (set it to the current);
	{
		switch ($span)
		{
			case 'day':
				$span_start_day = date('d');
				$span_start_month = date('m');
				$span_start_year = date('Y');
				$span_end_day = $span_start_day;
				$span_end_month = $span_start_month;
				$span_end_year = $span_start_year;
			break;
			case 'week':
				Get_Span_Limits(date('d'), date('m'), date('Y'), 'week', 0,
					$span_start_day, $span_start_month, $span_start_year,
					$span_end_day, $span_end_month, $span_end_year);
			break;
		}
	}

	if ($changed)
	{
		Get_Span_Limits($span_start_day, $span_start_month, $span_start_year, $span, $mult_factor,
			$span_start_day, $span_start_month, $span_start_year,
			$span_end_day, $span_end_month, $span_end_year);
	}

	switch ($span)
	{
		case 'day':{
			$result_xsl = "xsl/" . $default_xsl . "/groups_shedule_day.xsl";
		}break;
		case 'week':{
			$result_xsl = "xsl/" . $default_xsl . "/groups_schedule_week.xsl";
		}break;
	}

	$beg_day = "$span_start_year-$span_start_month-$span_start_day";
	$end_day = "$span_end_year-$span_end_month-$span_end_day";

	$span_start_day_of_week_id = date('w', mktime(1, 1, 1, $span_start_month, $span_start_day,
		$span_start_year));
	$span_end_day_of_week_id = date('w', mktime(1, 1, 1, $span_end_month, $span_end_day, $span_end_year));
	$span_start_day_of_week = $cfg['days_of_week'][$span_start_day_of_week_id];
	$span_end_day_of_week = $cfg['days_of_week'][$span_end_day_of_week_id];

	$u_beg_date = mktime(1, 1, 1, $span_start_month, $span_start_day, $span_start_year);
	$u_end_date = mktime(1, 1, 1, $span_end_month, $span_end_day, $span_end_year);

	$num_week = 0;
	$first_day_week = date('w', $u_beg_date);
	$num_day = $first_day_week;
	$weeks[0] = 0;

	$span_start_month_name = date('F', $u_beg_date);
	$span_end_month_name = date('F', $u_end_date);

	for ($u_date = $u_beg_date; $u_date <= $u_end_date; $u_date += 24 * 60 * 60, $num_day++)
	{
		$days[$num_day - $first_day_week][0] = $num_day;
		$days[$num_day - $first_day_week][1] = $num_week;
		$days[$num_day - $first_day_week][2] = date('d', $u_date);
		$days[$num_day - $first_day_week][3] = date('m', $u_date);
		$days[$num_day - $first_day_week][4] = date('Y', $u_date);
		if ( (($num_day+1) % 7) == 0)
		{
			$num_week++;
			$weeks[$num_week] = $num_week;
		}
		$days[$num_day - $first_day_week][5] = date('w', $u_date);
		$day_of_week = $days[$num_day][5];
		$days[$num_day - $first_day_week][6] = $cfg['days'][$day_of_week];
	}

	$apps[0] = Group_Free_Schedule($group_id, $beg_day, $end_day, '', '', $cfg['time'], $cfg['days'], $bd);
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$groups = List_Groups($group_id, '', '', '', 0, $bd);

$time_count = count($cfg['time']);

for ($i = 0; $i < ($time_count -1); $i++)
	$new_time[$i] = $cfg['time'][$i];

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpMainTime", $new_time);
$smarty->assign("nrpMainErrors", $error);
$smarty->assign("nrpMainAlerts", $alert);
$smarty->assign("nrpSpanStart", $span_start);
$smarty->assign("nrpSpanEnd", $span_end);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpSchSpan", $span);
$smarty->assign("nrpSpanStartDay", $span_start_day);
$smarty->assign("nrpSpanStartMonth", $span_start_month);
$smarty->assign("nrpSpanStartYear", $span_start_year);
$smarty->assign("nrpSpanEndYear", $span_end_year);
$smarty->assign("nrpSpanEndMonth", $span_end_month);
$smarty->assign("nrpSpanEndDay", $span_end_day);
$smarty->assign("nrpSpanStartDayOfWeek", $span_start_day_of_week);
$smarty->assign("nrpSpanEndDayOfWeek", $span_end_day_of_week);
$smarty->assign("nrpSpanStartDayOfWeekId", $span_start_day_of_week_id);
$smarty->assign("nrpSpanEndDayOfWeekId", $span_end_day_of_week_id);
$smarty->assign("nrpSpanStartMonthName", $span_start_month_name);
$smarty->assign("nrpSpanEndMonthName", $span_end_month_name);
$smarty->assign("nrpMainDays", $days);
$smarty->assign("nrpMainDaysOfWeek", $cfg['days']);
$smarty->assign("nrpMainApp", $apps);
$smarty->assign("nrpMainType", $cfg['user_type']);
$smarty->assign("nrpMainTypeColor", $cfg['user_color']);
$smarty->assign("nrpMainTypeIcon", $cfg['user_icon']);
$smarty->assign("nrpGroups", $groups);

$result_xml = $smarty->fetch("xml/groups_sched.xml");

require("./inc/proc_transform.php");
// Calls the commands to process the XSLT transformation
?>
