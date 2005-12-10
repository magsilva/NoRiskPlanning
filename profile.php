<?php
if ($already_initialized != 1)
// If another script has included this before
{
	require("./inc/script_inicialization.php");
}
require("./inc/nrp_api.php");

if (!isset($num_errors))
	$num_errors = 0;
if (!isset($num_alerts))
	$num_alerts = 0;

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);
	if (empty($_POST['submit']))
	// If the submit button wasn't pressed
	{
		$query = "SELECT * FROM accounts WHERE account_id = '$account_id'";
		$result = $bd->Query($query);
		$query1 = "SELECT * FROM people WHERE account_id = '$account_id'";
		$result1 = $bd->Query($query1);
		$name = $bd->FetchResult($result, 0, 'name');
		$dep_id = $bd->FetchResult($result1, 0, 'dep_id');
		$role = $bd->FetchResult($result, 0, 'role');
		$email = $bd->FetchResult($result1, 0, 'email');
		$url = $bd->FetchResult($result1, 0, 'url');
		$category_id = $bd->FetchResult($result1, 0, 'category');
		$cat = List_Categories($category_id, '', $bd);
		$category = $cat[0][1];
		$commentaries = $bd->FetchResult($result, 0, 'commentaries');
		$image_enable = $bd->FetchResult($result1, 0, 'public_types');
		$image_enable = "$image_enable";
		$type_array = $role . "_type";
	}
	else
	{
		$query = "SELECT * FROM accounts WHERE account_id = '$account_id'";
		$result = $bd->Query($query);
		$query1 = "SELECT * FROM people WHERE account_id = '$account_id'";
		$result1 = $bd->Query($query1);
		$name = $_POST['name'];
		$dep_id = $bd->FetchResult($result1, 0, 'dep_id');
		$role = $bd->FetchResult($result, 0, 'role');
		$email = $_POST['email'];
		$url = $_POST['url'];
		$category_id = $bd->FetchResult($result1, 0, 'category');
		$cat = List_Categories($category_id, '', $bd);
		$cur_password = $_POST['cur_password'];
		$category = $cat[0][1];
		$commentaries = $_POST['commentaries'];

		$type_array = $role . "_type";

		$image_enable = "";

		for ($i = 0; $cfg[$type_array][$i]; $i++)
		{
			$field_type = $cfg[$type_array][$i];
			if ($_POST[$field_type] == 'on')
				$image_enable .= "1";
			else
				$image_enable .= "0";
		}

		if (!User_Authenticate_Password($account_id, $cur_password, $bd))
			$error[$num_errors++] = "The Current Password is Wrong";

		switch (User_Validate_Simple_Field($name, 50))
		{
			case 0: $error[$num_errors++] = "The name is blank"; break;
			case -1: $error[$num_errors++] = "The name length is more than 50 characters"; break;
		}
		switch (User_Validate_Email($email, 70))
		{
			case 0: $error[$num_errors++] = "The e-mail is invalid"; break;
			case -1: $error[$num_errors++] = "The e-mail length is more than 70 characters"; break;
			case 1:{
				$person = List_People('', '', $email, '', '', $bd);
				if ($person)
				{
					if ($person[0][0] != $account_id)
						$error[$num_errors++] = "This E-mail is already in use";
				}
			}break;
		}

		if ($_POST['new_password'] != $_POST['conf_new_password'])
			$error[$num_errors++] = "The confirmation of the new password is not equal to the new password";

		if (!empty($_POST['new_pasword']))
		{
			switch (User_Validate_Password($_POST['new_passord']))
			{
				case 0: $error[$num_errors++] = "The New Password is blank"; break;
				case 1: $error[$num_errors++] = "The New Password is smaller than 6 characteres"; break;
			}
		}

		if (empty($error))
		{
			if (empty($_POST['new_password']))
				$new_password = '';
			else
				$new_password = $_POST['new_password'];

			Person_Update_Profile($account_id, $dep_id,  $name, $role, $email, $url, $category_id, $commentaries,
			 	$new_password, $image_enable, $bd);

			$alert[$num_alerts++] = "Profile Updated Successfully";
			include "main.php";
			exit;
		}
	}

	$query = "SELECT * FROM departments where dep_id = '$dep_id'";
	$result = $bd->Query($query);
 	$dep_name = $bd->FetchResult($result, 0, 'name');
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$result_xsl = "xsl/" . $cfg['default_xsl'] . "/profile.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpMainErrors", $error);
$smarty->assign("nrpMainAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);

$smarty->assign("nrpEnableTypes", $cfg[$type_array]);
$smarty->assign("nrpImageEnableValue", $image_enable);

$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpUserName", $name);
$smarty->assign("nrpRole", $role);
$smarty->assign("nrpDepId", $dep_id);
$smarty->assign("nrpUserDepName", $dep_name);
$smarty->assign("nrpEmail", $email);
$smarty->assign("nrpUrl", $url);
$smarty->assign("nrpCategoryId", $category_id);
$smarty->assign("nrpCategory", $category);
$smarty->assign("nrpComments", $commentaries);

$result_xml = $smarty->fetch("xml/profile.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
