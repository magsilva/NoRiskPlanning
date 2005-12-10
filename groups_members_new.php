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

if (!empty($_GET['group_id']))
	$group_id = $_GET['group_id'];
elseif (!empty($_POST['group_id']))
	$group_id = $_POST['group_id'];

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);

	$membership = Member_Role($group_id, $account_id, $bd);
	// If the user is actually member of the group

	if (!($membership == 'O' || $membership == 'M') )
	{
		$error[$num_errors++] = "You are not moderator of this group";
		include "groups.php";
		exit;
	}
	else
	{
		$groups = List_Groups($group_id, '', '', '', 1, $bd);
		if (!empty($_POST['insert']))
		{
			srand((double)microtime()*10000000);
			$conf_code = rand(000000,9999999);

			$invited = List_People($_POST['member_id'], '', '', '', '', $bd);
			$current_person = List_People($account_id, '', '', '', '', $bd);

			$id = $current_person[0][0];
			$name = $current_person[0][1];
			$adm_address = $cfg['admin_email'];
			$url = $cfg['url'];
			$group_name = $groups[0][1];
			$invited_name = $invited[0][1];

			mail($invited[0][5], "Invite to join group at No Risk Planning",
"Dear $invited_name,\n\n
You were invited to join the group $group_name at No Risk Planning.\n
To joint this group, enter the system using the URL:\n
$url, enter the option 'groups', choose the group you were invited,\n
confirm your membership.",
"From: No Risk Planning <$adm_address>");

			Group_Insert_Member($group_id, $_POST['member_id'], 'I', $conf_code, $bd);
			$alert[$num_alerts++] = "Member inserted succesfully";
			include "groups_members.php";
			exit;
		}
		$people = List_People('', '', '', '', '', $bd);
	}
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$result_xsl = "xsl/" . $cfg['default_xsl'] . "/groups_members_new.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchedErrors", $error);
$smarty->assign("nrpSchedAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpGroups", $groups);
$smarty->assign("nrpPeople", $people);

$result_xml = $smarty->fetch("xml/groups.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
