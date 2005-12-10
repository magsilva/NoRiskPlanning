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
	// If the submit button to create the unit was pressed
	{
		$current_password = $_POST['current_password'];
		$new_password = $_POST['new_password'];
		$conf_password = $_POST['conf_password'];

		switch (User_Validate_Simple_Field($current_password, 20))
		{
			case 0: $error[$num_errors++] = "The Current Password must be filled"; break;
		}

		switch (User_Validate_Simple_Field($new_password, 20))
		{
			case 0: $error[$num_errors++] = "The New Password must be filled"; break;
		}

		switch (User_Validate_Simple_Field($conf_password, 20))
		{
			case 0: $error[$num_errors++] = "The Password Confirmation must be filled"; break;
		}

		if ($new_password != $conf_password)
		{
			$error[$num_errors++] = "The password confirmation is not equal to the new password";
		}

		if (empty($error))
		{
			if (Admin_Set_Password($current_password, $new_password, $bd))
			{
				$alert[$num_alerts++] = "Password Updated Successfully";
				include "adm_main.php";
				exit;
			}
			else
			{
				$error[$num_errors] = "The current password is wrong";
			}
		}
	}

	$result_xsl = "xsl/" . $default_xsl . "/adm_password.xsl";
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchedErrors", $error);
$smarty->assign("nrpSchedAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);

$result_xml = $smarty->fetch("xml/scheduling.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
