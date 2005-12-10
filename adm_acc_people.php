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

	$people = List_People('', '',  '', '', '', $bd);
	$categories = List_Categories('', '', $bd);
	$departments = List_Departments('', '', '', $bd);
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$result_xsl = "xsl/" . $default_xsl . "/adm_acc_people.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchErrors", $error);
$smarty->assign("nrpSchAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpPeople", $people);
$smarty->assign("nrpCategories", $categories);
$smarty->assign("nrpDepartments", $departments);

$result_xml = $smarty->fetch("xml/adm_acc_people.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
