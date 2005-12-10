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
else if (!empty($_GET['group_id']))
	$group_id = $_GET['group_id'];

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);

	$membership = Member_Role($group_id, $account_id, $bd);
	// If the user is actually member of the group

	if ($membership == -1)
	{
		$error[$num_errors++] = "You are not member of this group";
		include "groups.php";
		exit;
	}
	else
	{
		$groups = List_Groups($group_id, '', '', '', 1,$bd);

		$notices = List_Notices($group_id, '', $cfg['group_notices_number'], $bd);

		switch($membership)
		{
			case 'O': $result_xsl = "xsl/" . $cfg['default_xsl'] . "/groups_enter_owner.xsl"; break;
			case 'M': $result_xsl = "xsl/" . $cfg['default_xsl'] . "/groups_enter_mod.xsl"; break;
			case 'C': $result_xsl = "xsl/" . $cfg['default_xsl'] . "/groups_enter_com.xsl"; break;
			case 'I': $result_xsl = "xsl/" . $cfg['default_xsl'] . "/groups_members_confirmation.xsl"; break;
		}
	}
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
$smarty->assign("nrpGroups", $groups);
$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpNotices", $notices);

$result_xml = $smarty->fetch("xml/groups.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
