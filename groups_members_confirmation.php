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

if (!empty($_POST['group_id']))
	$group_id = $_POST['group_id'];
elseif (!empty($_GET['group_id']))
	$group_id = $_GET['group_id'];

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);

	$membership = Member_Role($group_id, $account_id, $bd);
	// If the user is actually member of the group

	if ($membership != 'I')
	{
		$error[$num_errors++] = "There is no membership confirmation to be done at this group.";
		include "groups.php";
		exit;
	}
	else
	{
		if (!empty($_POST['submit_conf_yes']) || !empty($_POST['submit_conf_no']))
		// If the submit button to confirm the appointment deletion was pressed
		{
			if (!empty($_POST['submit_conf_yes']))
			{
				// Make the member to become a common member of the group
				Group_Change_Membership($group_id, $account_id, 'C', $bd);
				$alert[$num_alerts++] = 'You were included in the group.';
				include "groups_enter.php";
			}
			else
			{
				// Removes the invitation
				Group_Remove_Member($group_id, $account_id, $bd);
				$alert[$num_alerts++] = 'Your invitation to the group was cancelled.';
				include "groups.php";
			}
			exit;
		}
		else
		{
			$error[$num_errors++] = "Invalid confirmation code.";
		}
		$groups = List_Groups($group_id, '', '', '', 1, $bd);
	}
	$people = List_People($account_id, '', '', '', '', $bd);
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$result_xsl = "xsl/" . $cfg['default_xsl'] . "/groups_members_confirmation.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchedErrors", $error);
$smarty->assign("nrpSchedAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpPeople", $people);
$smarty->assign("nrpGroups", $groups);

$result_xml = $smarty->fetch("xml/groups.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
