<?php
if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
}

if (!isset($num_errors))
	$num_errors = 0;
if (!isset($num_alerts))
	$num_alerts = 0;

$conf = $_GET['conf'];
$user = $_GET['user'];

$query = "SELECT * FROM people WHERE account_id = '$user'";
$result = $bd->Query($query);

if ($bd->NumberOfRows($result) > 0)
{
	$curr_passwd = $bd->FetchResult($result, 0, 'password');
	$email = $bd->FetchResult($result, 0, 'email');

	$c_conf = 0;
	$limit = strlen($curr_passwd);
	for ($i = 0; $i < $limit; $i++)
		$c_conf += ord($curr_passwd[$i]);

	if ($c_conf == $conf)
	{
		srand((double)microtime()*1000000);
		$password = rand(000000,999999);
		$passwordmd5 = md5($password);

		$id = $user;
		$adm_address = $cfg['admin_email'];
		$url = $cfg['url'];

		mail($email, "No Risk Planning - Password Recovery",
"According to your request, these are your login and your new password:.\n
Login: $id\nPassword: $password\n\n
To get into the system enter $url.",
"From: No Risk Planning <$adm_address>");

		$bd->Query("UPDATE people SET password = '$passwordmd5' WHERE account_id = '$user'");

		$alert[$num_alerts++] = "A new password was sent to your email";
	}
	else
		$error[$num_errors++] = "Invalid confirmation code";
}
else
	$error[$num_errors++] = "User not found";
$result_xsl = "xsl/" . $default_xsl . "/logout.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpIndexAlerts", $alert);
$smarty->assign("nrpIndexErrors", $error);

$result_xml = $smarty->fetch("xml/index.xml");

       require("./inc/proc_transform.php");
?>
