<?php
require("./inc/script_inicialization.php");
require("./inc/nrp_api.php");

$num_errors = 0;
// Number of errors

$num_alerts = 0;
// Number of alerts


if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);

	$slave_role = Get_Role($_GET['slave_id'], $bd);
	
	$has_permission = 0;
	if ($slave_role == 'course')
	{
		$courses = User_List_Courses($account_id, $bd);
		for ($i = 0; ($i < count($courses)) && !$has_permission; $i++)
		{
			if ($courses[$i][0] == $_GET['slave_id'])
				$has_permission = 1;
		} 
	
	}
	if ($slave_role == 'room')
	{
		$rooms = User_List_Rooms($account_id, $bd);
		for ($i = 0; ($i < count($rooms)) && !$has_permission; $i++)
		{
			if ($rooms[$i][0] == $_GET['slave_id'])
				$has_permission = 1;
		}
	}
	
	if ($has_permission)
	{
		$slave_sess_id = Create_Session($_GET['slave_id'], 'default', $_SERVER['REMOTE_ADDR'], $sess_id, $bd);
		$crypt_sess_id = md5($slave_sess_id);
		$complete_sess_id = $crypt_sess_id . $slave_sess_id;
		
		header("location:main.php?sess_id=$complete_sess_id");
		exit;
	}
	else
	{
		$error[$num_errors++] = "You do not have permission to handle this account.";
		include "scheduling.php";
		exit;
	}

}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

?>
