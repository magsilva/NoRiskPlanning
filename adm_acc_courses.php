<?php
if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
	require("./inc/nrp_api.php");
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

	$courses = List_Courses('', '',  '', '', '', '', '', $bd);
	$category = List_Categories('', $cfg['professor_category'], $bd);
	$people = List_People('', '', '', '', $category[0][0], $bd);
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$result_xsl = "xsl/" . $default_xsl . "/adm_acc_courses.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchErrors", $error);
$smarty->assign("nrpSchAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpCourses", $courses);
$smarty->assign("nrpPeople", $people);

$result_xml = $smarty->fetch("xml/adm_acc_courses.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
