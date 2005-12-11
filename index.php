<?php
require("./inc/script_inicialization.php");
require("./inc/nrp_api.php");

$num_errors = 0;
$num_alerts = 0;

if (isset($_REQUEST['submit']) && $_REQUEST['submit'] == "Login")
{
	ob_start();

	$val_id = User_Validate_Simple_Field($_REQUEST['id'], 32);
	$val_password = User_Validate_Password($_REQUEST['password'], 6);
	$user_ok = 0;

	if ($val_id && $val_password)
	{
		$auth_result = User_Authenticate_Password($_REQUEST['id'], $_REQUEST['password'], $bd);
		if ($auth_result == 1)
			$user_ok = 1;
		elseif ($auth_result == 0)
			$error[$num_errors++] = "Wrong Password";
		else
			$error[$num_errors++] = "User Not Found";
	}
	else
	{
		if ($val_id == 0)
			$error[$num_errors++] = "The User ID is blank";
		else if ($val_id == -2)
			$error[$num_errors++] = "There are invalid characters in the User ID";
		if ($val_password == 0)
			$error[$num_errors++] = "The password is blank";
		elseif ($val_password == -1)
			$error[$num_errors++] = "The password must be at least 6 characters in length";
	}

	if ($user_ok)
	{
		$is_admin = 0;
		if ($_REQUEST['id'] == 'admin')
			$is_admin = 1;
		$ip_address = $_SERVER['REMOTE_ADDR'];
		$sess_id = Create_Session($id, 'default', $ip_address, 0, $bd);
		$crypt_sess_id = md5($sess_id);
		$complete_sess_id = $crypt_sess_id . $sess_id;

		if ($is_admin)
		{
			header("location: adm_main.php?sess_id=$complete_sess_id");
			exit;
		}
		else
		{
			header("location: main.php?sess_id=$complete_sess_id");
			exit;
		}
	}
}


// If the user asked to be mailed his password
if (isset($_REQUEST['send']) && $_REQUEST['send'] == "Sogin")
{
	$email = User_Validate_Email($_REQUEST['email']);
	$email_sql = $bd->GetTextFieldValue($email);

	$query = "SELECT accounts.account_id, accounts.name, people.email, people.password
		FROM accounts, people WHERE people.email=$email_sql and (accounts.account_id = people.account_id)";
	$result = $bd->Query($query);

	if ($result != 0 && $bd->NumberOfRows($result) != 0 )
	{
		$user = $bd->FetchResult($result, 0, 'account_id');
		$curr_passwd = $bd->FetchResult($result, 0, 'password');
		$conf = 0;
		$limit = strlen($curr_passwd);
		for ($i = 0; $i < $limit; $i++)
			$conf += ord($curr_passwd[$i]);

		$addr = $cfg['url']."passwd_recovery.php?user=" . $user . "&conf=".$conf;
		$mail_admin = $cfg['admin_email'];
		$institution = $cfg['institution_acronym'];

		$mail_content =  "There is a solicitation of password recovering on your account at No Risk Planning. \nIf you really asked for it, click on the following link $addr to get your login and password. \n\n";

		mail($email, "No Risk Planning - Password Recovery - $institution", $mail_content, "From: No Risk Planning <$mail_admin>");

		$alert[$num_alerts++] = "A password request confirmation has been sent to your e-mail.";
	}
	else
	{
		$error[$num_errors++] = "This e-mail wasn't found in the database!";
	}
}

$result_xsl = "xsl/" . $cfg['default_xsl'] . "/index.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpIndexErrors", $error);
$smarty->assign("nrpIndexAlerts", $alert);
if (isset($_REQUES['id']))
	$smarty->assign("nrpUserId", $_REQUEST['id']);
$result_xml = $smarty->fetch("xml/index.xml");

if (!isset($_REQUEST['submit']) || isset($error))
{
	require("./inc/proc_transform.php");
}
?>
