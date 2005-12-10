<?php
if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
	require_once("./inc/nrp_api.php");
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

	if (!empty($_POST['submit_conf_yes']) || !empty($_POST['submit_conf_no']))
	// If the submit button to confirm the appointment deletion was pressed
	{
		if (!empty($_POST['submit_conf_yes']))
		{
			if (Delete_Department($_POST['dep_id'], $bd) == 1)
			{
				$alert[$num_alerts++] = 'Department deleted successfully';
			}
			else{
				$error[$num_errors++] = 'There are users linked to this department, so it cannot be removed';
			}
		}
		else
		{
			$alert[$num_alerts++] = 'Department deletion cancelled';
		}
		include "adm_departments.php";
		exit;
	}
	else if (!empty($_GET['dep_id']))
	// If the submit button to choose the appointment was pressed
	{
		$departments = List_Departments($_GET['dep_id'], '', '', $bd);
	}

	$result_xsl = "xsl/" . $default_xsl . "/adm_departments_remove.xsl";
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
$smarty->assign("nrpMasterSessId", $master_session);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpUnits", $units);
$smarty->assign("nrpDepartments", $departments);

$result_xml = $smarty->fetch("xml/adm_departments.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
