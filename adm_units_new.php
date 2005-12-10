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

	if (!empty($_POST['create']))
	// If the submit button to create the unit was pressed
	{
		$units[0][0] = '';
		$units[0][1] = $_POST['name'];
		$units[0][2] = $_POST['acronym'];
		$units[0][3] = $_POST['description'];


		switch (User_Validate_Simple_Field($units[0][1], 50))
		{
			case 0: $error[$num_errors++] = "The Name must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the name"; break;
			case 1:{
				$current = List_Units('', $units[0][1], $bd);
				if ($current)
					$error[$num_errors++] = "This unit name is already in use";
			}break;
		}

		switch (User_Validate_Simple_Field($units[0][2], 15))
		{
			case 0: $error[$num_errors++] = "The Acronym must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the acronym"; break;
		}

		if (empty($error))
		{
			Insert_Unit($units[0][1], $units[0][2], $units[0][3], $bd);
			$alert[$num_alerts++] = "New Unit Inserted Successfully";
			include "adm_units.php";
			exit;
		}
	}
	$result_xsl = "xsl/" . $default_xsl . "/adm_units_new.xsl";
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

$result_xml = $smarty->fetch("xml/adm_units.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
