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

if (!empty($_GET['notice_id']))
	$notice_id = $_GET['notice_id'];
else if (!empty($_POST['notice_id']))
	$notice_id = $_POST['notice_id'];

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);

	$membership = Member_Role($group_id, $account_id, $bd);

	// If the user is actually moderator or owner of the group
	if (! (($membership == 'O') || ($membership == 'M')) )
	{
		$error[$num_errors++] = "You are not moderator of this group.";
		include "groups.php";
		exit;
	}

	if (!empty($_POST['submit_conf_yes']) || !empty($_POST['submit_conf_no']))
	// If the confirmation was done
	{
		if (!empty($_POST['submit_conf_yes']))
		{
			Remove_Notice($notice_id, $bd);
			$alert[$num_alerts++] = "Notice Removed Successfully";
		}
		else
			$alert[$num_alerts++] = "Notice Removing Cancelled";
		include "groups_notices.php";
		exit;
	}

	$groups = List_Groups($group_id, '', '', '', 1, $bd);

	$notices = List_Notices('', $notice_id, '', $bd);
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$result_xsl = "xsl/" . $default_xsl . "/groups_notices_remove.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchErrors", $error);
$smarty->assign("nrpSchAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpGroups", $groups);
$smarty->assign("nrpPeople", $people);
$smarty->assign("nrpNotices", $notices);

$result_xml = $smarty->fetch("xml/groups.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
