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

if (!empty($_GET['account_id']))
	$member_id = $_GET['account_id'];
else if (!empty($_POST['account_id']))
	$member_id = $_POST['account_id'];

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);

	$membership = Member_Role($group_id, $account_id, $bd);

	$ch_membership = Member_Role($group_id, $member_id, $bd);

	// If the user is actually moderator or owner of the group
	if (! (($membership == 'O') || ($membership == 'M')) )
	{
		$error[$num_errors++] = "You are not moderator of this group.";
		include "groups.php";
		exit;
	}

	if (  $ch_membership == 'O' )
	{
		$error[$num_errors++] = "The Owner membership may not be changed.";
		include "groups.php";
		exit;
	}

	if (!empty($_POST['modify']))
	// If the submit button to create the branch was pressed
	{
		Group_Change_Membership($group_id, $member_id, $_POST['membership'], $bd);
		$alert[$num_alerts++] = "Membership Changed Successfully";
		include "groups_members.php";
		exit;
	}

	$groups = List_Groups($group_id, '', '', '', 1, $bd);

	$groups[0][5] = List_Group_Member($group_id, $member_id, $bd);
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$result_xsl = "xsl/" . $default_xsl . "/groups_members_modify.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchErrors", $error);
$smarty->assign("nrpSchAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpGroups", $groups);
$smarty->assign("nrpPeople", $people);

$result_xml = $smarty->fetch("xml/groups.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
