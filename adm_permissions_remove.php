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
	// If the submit button to confirm the permition deletion was pressed
	{
		if (!empty($_POST['submit_conf_yes']))
		{
			Delete_Permission($_POST['perm_id'], $bd);
			$alert[$num_alerts++] = 'Permission deleted successfully';
		}
		else
		{
			$alert[$num_alerts++] = 'Permission deletion cancelled';
		}
		include "adm_permissions.php";
		exit;
	}
	else if (!empty($_GET['perm_id']))
	// If the submit button to choose the appointment was pressed
	{
		$permissions = List_Permissions($_GET['perm_id'], '', '', '', '', '', $bd);
	}

	$result_xsl = "xsl/" . $default_xsl . "/adm_permissions_remove.xsl";
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
$smarty->assign("nrpPermissions", $permissions);
$smarty->assign("nrpCategories", $categories);
$smarty->assign("nrpPeople", $people);
$smarty->assign("nrpGroups", $groups);
$smarty->assign("nrpCourses", $courses);
$smarty->assign("nrpRooms", $rooms);

$result_xml = $smarty->fetch("xml/adm_permissions.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
