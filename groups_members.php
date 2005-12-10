<?php
if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
	require_once("./inc/nrp_api.php");
}

if (!empty($_POST['group_id']))
	$group_id = $_POST['group_id'];
elseif (!empty($_GET['group_id']))
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
		$groups = List_Groups($group_id, '', '', '', 1, $bd);

		switch($membership)
		{
			case 'O': $result_xsl = "xsl/" . $cfg['default_xsl'] . "/groups_members_mod.xsl"; break;
			case 'M': $result_xsl = "xsl/" . $cfg['default_xsl'] . "/groups_members_mod.xsl"; break;
			case 'C': $result_xsl = "xsl/" . $cfg['default_xsl'] . "/groups_members.xsl"; break;
			case 'I': $result_xsl = "xsl/" . $cfg['default_xsl'] . "/groups_members.xsl"; break;
		}
	}

	$people = array();

	for ($i = 0; $groups[0][5][$i]; $i++)
	{
		$people = array_merge($people, List_People($groups[0][5][$i][0], '', '', '', '', $bd));
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
$smarty->assign("nrpPeople", $people);
$smarty->assign("nrpGroups", $groups);

$result_xml = $smarty->fetch("xml/groups.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
