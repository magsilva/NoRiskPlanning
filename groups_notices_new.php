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

	$groups = List_Groups($group_id, '', '', '', 1, $bd);
	if (!empty($_POST['insert']))
	{
		if (empty($_POST['description']))
			$error[$num_errors++] = "The notice text may not be blank";
		else
		{
			for ($i = 0; $groups[0][5][$i]; $i++)
			{
				$member = List_People($_POST['member_id'], '', '', '', '', $bd);
				$current_person = List_People($account_id, '', '', '', '', $bd);
	
				$notice_desc = $_POST['description'];
				$name = $current_person[0][1];
				$adm_address = $cfg['admin_email'];
				$group_name = $groups[0][1];
				$invited_name = $member[0][1];
	
				mail($member[0][5], "Group notice from $group_name at No Risk Planning",
"Dear $invited_name,\n\n
$name has sent the following notice to group $group_name:\n\n
$notice_desc\n"
,"From: No Risk Planning <$adm_address>");
			}
		
			Insert_Notice($group_id, $account_id, $_POST['description'], $bd);
			$alert[$num_alerts++] = "Notice inserted succesfully";
			include "groups_notices.php";
			exit;
		}
	}
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$result_xsl = "xsl/" . $cfg['default_xsl'] . "/groups_notices_new.xsl";

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
