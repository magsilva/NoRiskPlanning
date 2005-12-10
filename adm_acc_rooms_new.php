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
		$rooms[0][0] = $_POST['account_id'];
		$rooms[0][1] = $_POST['name'];
		$rooms[0][2] = $_POST['comments'];
		$rooms[0][3] = $_POST['code'];
		$rooms[0][4] = $_POST['capacity'];
		$rooms[0][5] = $_POST['location'];
		$rooms[0][6] = $_POST['type'];

		switch (User_Validate_Simple_Field($rooms[0][0], 32))
		{
			case 0: $error[$num_errors++] = "The account_id must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the account_id"; break;
			case 1:{
				$current = Check_Account_Id($rooms[0][0], $bd);
				if ($current || ($rooms[0][0] == 'admin'))
					$error[$num_errors++] = "This account id is already in use";
			}break;
		}

		switch (User_Validate_Simple_Field($rooms[0][1], 15))
		{
			case 0: $error[$num_errors++] = "The Name must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the name"; break;
		}

		switch (User_Validate_Simple_Field($rooms[0][2], 256))
		{
			case -2: $error[$num_errors++] = "There are invalid characteres at the comments"; break;
		}

		switch (User_Validate_Simple_Field($rooms[0][5], 100))
		{
			case -2: $error[$num_errors++] = "There are invalid characteres at the location"; break;
		}

		switch (User_Validate_Numeric_Field($rooms[0][4]))
		{
			case -1: $error[$num_errors++] = "The capacity must be a integer greater than 0"; break;
			case 0: $error[$num_errors++] = "The capacity must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the location"; break;
		}

		if (empty($error))
		{

			Insert_Room($rooms[0][0], $rooms[0][1], $rooms[0][2], $rooms[0][3], $rooms[0][4],
				$rooms[0][5], $rooms[0][6], $bd);
			$alert[$num_alerts++] = "New Room Inserted Successfully";
			include "adm_acc_rooms.php";
			exit;
		}
	}

	$result_xsl = "xsl/" . $default_xsl . "/adm_acc_rooms_new.xsl";
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
$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpRooms", $rooms);

$result_xml = $smarty->fetch("xml/adm_acc_rooms.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
