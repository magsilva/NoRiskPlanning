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

	if (!empty($_POST['modify']))
	// If the submit button to create the branch was pressed
	{
		$departments[0][0] = $_POST['dep_id'];
		$departments[0][1] = $_POST['name'];
		$departments[0][2] = $_POST['acronym'];
		$departments[0][3] = $_POST['description'];
		$departments[0][4] = $_POST['unit_id'];

		switch (User_Validate_Simple_Field($departments[0][1], 50))
		{
			case 0: $error[$num_errors++] = "The Name must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the name"; break;
			case 1:{
				$current = List_Departments('', $departments[0][1], '', $bd);
				if ($current)
					if ($current[0][0] != $branchs[0][0])
						$error[$num_errors++] = "This department name is already in use";
			}break;
		}

		switch (User_Validate_Simple_Field($departments[0][2], 15))
		{
			case 0: $error[$num_errors++] = "The Acronym must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the acronym"; break;
		}

		if (empty($error))
		{
			Update_Department($departments[0][0], $departments[0][1], $departments[0][2],
				$departments[0][3], $departments[0][4], $bd);
			$alert[$num_alerts++] = "Department Updated Successfully";
			include "adm_departments.php";
			exit;
		}
	}
	else{
		$departments = List_Departments($_GET['dep_id'], '', '', $bd);
	}
	$units = List_Units('', '', $bd);
	$result_xsl = "xsl/" . $default_xsl . "/adm_departments_modify.xsl";
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
