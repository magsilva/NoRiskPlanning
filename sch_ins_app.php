<?php

if (!empty($_POST['switch']))
{
	include "sch_ins_week_app.php";
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

$master_session = Get_Crypt_Master_Session($sess_id, $bd);

if ($_POST['ins_at_master'] == 'on')
	$ins_at_master = 1;
else
	$ins_at_master = 0;

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
	
	$m_session = Get_Master_Session($sess_id, $bd);	

	if (!$master_session)
		$owner = $account_id;
	else
		Get_Account_Id($m_session, $owner, $bd);

	$query = "SELECT * FROM accounts WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	$role = $bd->FetchResult($result, 0, 'role');
	$var_type = $role . '_type';
	$var_color = $role . '_color';
	$var_image = $role . '_icon';
	$array_type = $cfg[$var_type];
	$array_color = $cfg[$var_color];
	$array_image = $cfg[$var_image];

	if ($is_pop)
		$result_xsl = "xsl/" . $default_xsl . "/sch_ins_app_pop.xsl";
	else
		$result_xsl = "xsl/" . $default_xsl . "/sch_ins_app.xsl";

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

		$check = Check_Appointment_Overlap($account_id, $_POST['day'], $_POST['month'],
			$_POST['year'], $_POST['beg_time'], $_POST['end_time'], '', $bd);

		if ($check)
		{
			$error[$num_errors++] = "There is another appointment within this time span";
		}


		if ($ins_at_master)
		{
			$check = Check_Appointment_Overlap($owner, $_POST['day'], $_POST['month'],
				$_POST['year'], $_POST['beg_time'], $_POST['end_time'], '', $bd);
				if ($check)
					$error[$num_errors++] = "There is another appointment on the owner's schedule within this time span";
		}
	
		if (empty($error))
		{
			$day_w = date('w', mktime(0, 0, 0, $_POST['month'], $_POST['day'], $_POST['year']));
			$check = $check = Check_Weekly_Appointment_Overlap($account_id, $day_w,  $_POST['beg_time'],
				$_POST['end_time'], '', $bd);
			if ($ins_at_master)
			{
				$check1 = Check_Weekly_Appointment_Overlap($owner, $day_w, $_POST['beg_time'],
					$_POST['end_time'], '', $bd);
			}

			if (!$check1 && !$check)
			// If there aren't weekly appointments
			{
				Insert_Appointment($account_id, $_POST['description'], $_POST['type'],
					$_POST['year']."-".$_POST['month']."-".$_POST['day'], $_POST['beg_time'],
					$_POST['end_time'], $_POST['url'], $owner, 0, 0, 1,  $bd);
				$alert[$num_alerts++] = "Appointment Inserted Successfully";

				if ($ins_at_master)
				{
					Insert_Appointment($owner, $_POST['description'], $_POST['type'],
						$_POST['year']."-".$_POST['month']."-".$_POST['day'],
						$_POST['beg_time'], $_POST['end_time'], $_POST['url'], $owner,
						0, 0, 1, $bd);
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
			else
			{
				if ($is_pop)
					$result_xsl = "xsl/" . $default_xsl . "/sch_ins_app_pop1.xsl";
				else
					$result_xsl = "xsl/" . $default_xsl . "/sch_ins_app1.xsl";
			}
		}
	}
	elseif (!empty($_POST['submit_conf_yes']) || !empty($_POST['submit_conf_no']))
	{
		if (!empty($_POST['submit_conf_yes']))
		{
			Insert_Appointment($account_id, $_POST['description'], $_POST['type'],
				$_POST['year']."-".$_POST['month']."-".$_POST['day'], $_POST['beg_time'],
				$_POST['end_time'], $_POST['url'], $owner, 0, 0, 1, $bd);
			$alert[$num_alerts++] = "Appointment Inserted Successfully";
			if ($ins_at_master)
			{
				Insert_Appointment($owner, $_POST['description'], $_POST['type'],
					$_POST['year']."-".$_POST['month']."-".$_POST['day'],
					$_POST['beg_time'], $_POST['end_time'], $_POST['url'], $owner,
					0, 0, 1, $bd);
				$alert[$num_alerts++] = "Appointment Inserted Successfully at Owner's schedule";
			}
		}
		else
		{
			$alert[$num_alerts++] = 'Appointment Insertion Cancelled';

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
$smarty->assign("nrpGroupId", '');
$smarty->assign("nrpGroup", '');
$smarty->assign("nrpBefore", '');
$smarty->assign("nrpAfter", '');
$smarty->assign("nrpRoom", $_POST['room']);
$smarty->assign("nrpSchInsMaster", $ins_at_master);
$smarty->assign("nrpMasterSession", $master_session);
$smarty->assign("nrpSchMonths", $cfg['months']);

$result_xml = $smarty->fetch("xml/sch_ins_app.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
