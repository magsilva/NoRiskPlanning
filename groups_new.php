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

	if (!empty($_POST['create']))
	// If the submit button to create the unit was pressed
	{
		$groups[0][0] = '';
		$groups[0][1] = $_POST['name'];
		$groups[0][2] = $_POST['category'];
		$groups[0][3] = $_POST['acronym'];
		$people[0][4] = $_POST['description'];

		switch (User_Validate_Simple_Field($groups[0][1], 100))
		{
			case 0: $error[$num_errors++] = "The Group name must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the group name"; break;
			case 1:{
				$current = List_Groups('', $groups[0][1], '', '', 1, $bd);
				if ($current)
					$error[$num_errors++] = "This Group Name is already in use";
			}break;
		}

		switch (User_Validate_Simple_Field($groups[0][3], 15))
		{
			case 0: $error[$num_errors++] = "The Group Acronym must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the group acronym"; break;
			case 1:{
				$current = List_Groups('', '', '', $groups[0][3], 1, $bd);
				if ($current)
					$error[$num_errors++] = "This Group Acronym is already in use";
			}break;
		}

		switch (User_Validate_Simple_Field($groups[0][2], 256))
		{
			case -2: $error[$num_errors++] = "There are invalid characteres at the group category"; break;
		}

		switch (User_Validate_Simple_Field($groups[0][3], 256))
		{
			case -2: $error[$num_errors++] = "There are invalid characteres at the group description"; break;
		}

		if (empty($error))
		{
			Insert_Group($groups[0][1], $groups[0][2], $groups[0][3], $groups[0][4], $bd);
			$groups_new = List_Groups('', $groups[0][1], '', '', 1, $bd);
			Insert_Group_Member($groups_new[0][0], $account_id, 'O', '', $bd);
			$alert[$num_alerts++] = "New Group Inserted Successfully";
			include "groups.php";
			exit;
		}
	}

	$result_xsl = "xsl/" . $default_xsl . "/groups_new.xsl";
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
$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpGroups", $groups);

$result_xml = $smarty->fetch("xml/groups.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
