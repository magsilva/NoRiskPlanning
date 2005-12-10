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

	$groups = List_Groups($group_id, '', '', '', 0, $bd);
	if (!empty($_POST['insert']))
	{
		if (empty($_POST['description']))
			$error[$num_errors++] = "The document description may not be blank";
		if (empty($_FILES['userfile']['name']))
			$error[$num_errors++] = "The document may not be null";
		else
		{
			if ($_FILES['userfile']['size'] > $cfg['max_doc_size'])
				$error[$num_errors++] = "The maximum document size is ". $cfg['max_doc_size'] . "bytes";
		}

		if (empty($error))
		{
			$id = Insert_Document($group_id, $account_id, $_FILES['userfile']['size'],
				 $_FILES['userfile']['name'], $_POST['description'], $bd);
			@copy($_FILES['userfile']['tmp_name'], $cfg['directory'].$cfg['docs_directory'].
				$id."_".$_FILES['userfile']['name']);
			$alert[$num_alerts++] = "Document inserted succesfully";
			include "groups_documents.php";
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

$result_xsl = "xsl/" . $cfg['default_xsl'] . "/groups_documents_new.xsl";

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
