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

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);
	if ($account_id != 'admin')
	{
		$error[$num_errors++] = "You are not the administrator";
		include "logout.php";
		exit;
	}
}
else
{
		$error[$num_errors++] = "Invalid Session ID";
		include "logout.php";
		exit;
}

$query = "SELECT count(group_id) as num_groups FROM groups";
$result = $bd->Query($query);
$num_groups = $bd->FetchResult($result, 0, 'num_groups');

$query = "SELECT count(cat_id) as num_categ FROM categories";
$result = $bd->Query($query);
$num_categ = $bd->FetchResult($result, 0, 'num_categ');

$query = "SELECT count(unit_id) as num_units FROM units";
$result = $bd->Query($query);
$num_units = $bd->FetchResult($result, 0, 'num_units');

$query = "SELECT count(dep_id) as num_deps FROM departments";
$result = $bd->Query($query);
$num_deps = $bd->FetchResult($result, 0, 'num_deps');

$query = "SELECT count(account_id) as num_people FROM people";
$result = $bd->Query($query);
$num_people = $bd->FetchResult($result, 0, 'num_people');

$query = "SELECT count(account_id) as num_courses FROM courses";
$result = $bd->Query($query);
$num_courses = $bd->FetchResult($result, 0, 'num_courses');

$query = "SELECT count(account_id) as num_rooms FROM rooms";
$result = $bd->Query($query);
$num_rooms = $bd->FetchResult($result, 0, 'num_rooms');

$result_xsl = "xsl/" . $default_xsl . "/adm_main.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpSchedErrors", $error);
$smarty->assign("nrpSchedAlerts", $alert);
$smarty->assign("nrpNumGroups", $num_groups);
$smarty->assign("nrpNumCateg", $num_categ);
$smarty->assign("nrpNumUnits", $num_units);
$smarty->assign("nrpNumDeps", $num_deps);
$smarty->assign("nrpNumRooms", $num_rooms);
$smarty->assign("nrpNumCourses", $num_courses);
$smarty->assign("nrpNumPeople", $num_people);

$result_xml = $smarty->fetch("xml/adm_main.xml");

require("./inc/proc_transform.php");
// Calls the commands to process the XSLT transformation
?>
