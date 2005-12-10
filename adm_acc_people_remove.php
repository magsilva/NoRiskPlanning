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
			if (Delete_Person($_POST['account_id'], $bd) == 1)
			{
				$alert[$num_alerts++] = 'Person deleted successfully';
			}
		}
		else
		{
			$alert[$num_alerts++] = 'Person deletion cancelled';
		}
		include "adm_acc_people.php";
		exit;
	}
	else if (!empty($_GET['account_id']))
	{
		$people = List_People($_GET['account_id'], '', '', '', '',  $bd);
	}

	$result_xsl = "xsl/" . $default_xsl . "/adm_acc_people_remove.xsl";
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
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpPeople", $people);

$result_xml = $smarty->fetch("xml/adm_acc_people.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
