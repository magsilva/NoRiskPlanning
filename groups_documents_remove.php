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

if (!empty($_GET['document_id']))
	$document_id = $_GET['document_id'];
else if (!empty($_POST['document_id']))
	$document_id = $_POST['document_id'];

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	$documents = List_Documents('', $document_id, $cfg, $bd);

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
			unlink($cfg['directory'].$cfg['docs_directory'].$documents[0][0]."_".$documents[0][6]);
			Remove_Document($document_id, $bd);
			$alert[$num_alerts++] = "Document Removed Successfully";
		}
		else
			$alert[$num_alerts++] = "Document Removing Cancelled";
		include "groups_documents.php";
		exit;
	}

	$groups = List_Groups($group_id, '', '', '', 0, $bd);
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$result_xsl = "xsl/" . $default_xsl . "/groups_documents_remove.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchErrors", $error);
$smarty->assign("nrpSchAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpGroups", $groups);
$smarty->assign("nrpPeople", $people);
$smarty->assign("nrpDocuments", $documents);

$result_xml = $smarty->fetch("xml/groups.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
