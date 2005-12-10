<?php
ob_start();
//Start a flush of data (Used to solve problems with include-cookies)

require("./inc/script_inicialization.php");
require("./inc/nrp_api.php");

$num_errors = 0;
// Number of errors

$num_alerts = 0;
// Number of alerts

if ($_POST['submit'] == "Login")
{
	$val_id = User_Validate_Simple_Field($_POST['id'], 32);
	$val_password = User_Validate_Password($_POST['password'], 6);
	if ($val_id && $val_password){
		$auth_result = User_Authenticate_Password($_POST['id'], $_POST['password'], $bd);
		if ($auth_result == 1)
			$user_ok = 1;
		elseif ($auth_result == 0)
			$error[$num_errors++] = "Wrong Password";
		else
			$error[$num_errors++] = "User Not Found";
	}
	else{
		if ($val_id == 0){
			$error[$num_errors++] = "The User ID is blank";}
		elseif ($val_id == -2)
			$error[$num_errors++] = "There are invalid characters in the User ID";
		if ($val_id == 0)
			$error[$num_errors++] = "The password is blank";
		elseif ($val_id == -1)
			$error[$num_errors++] = "The password must be at least 6 characters in length";
	}
	if ($user_ok)
	{
		$is_admin = 0;
		if ($_POST['id'] == 'admin')
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

if ($send == "Send")
// If the user asked to be mailed his password
{
	$query = "SELECT accounts.account_id, accounts.name, people.email, people.password
		FROM accounts, people WHERE (accounts.account_id = people.account_id) and email='" . $_POST['email'] . "'";
	$result = $bd->Query($query);

	if ( $bd->NumberOfRows($result) == 0 )
	{
		$error[$num_errors++] = "This e-mail wasn't found in the database!";
	}
	else {
		$user = $bd->FetchResult($result, 0, 'account_id');
		$curr_passwd = $bd->FetchResult($result, 0, 'password');
		$email = $_POST['email'];
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
}

$result_xsl = "xsl/" . $cfg['default_xsl'] . "/index.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpIndexErrors", $error);
$smarty->assign("nrpIndexAlerts", $alert);
$smarty->assign("nrpUserId", $_POST['id']);

$result_xml = $smarty->fetch("xml/index.xml");

if (!$submit || $error)
// If the submit button wasn't pressed or there was some error
{
	require("./inc/proc_transform.php");
	// Calls the commands do procede the XSLT transformation
}
ob_end_flush();
?>
