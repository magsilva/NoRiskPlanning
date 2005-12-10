<?php
if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
	include "./inc/nrp_api.php";
}

if (!isset($num_errors))
	$num_errors = 0;
if (!isset($num_alerts))
	$num_alerts = 0;

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{

}

$result_xsl = "xsl/" . $default_xsl . "/admin_main.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSessId", $complete_sess_id);

$result_xml = $smarty->fetch("xml/main.xml");

require("./inc/proc_transform.php");
// Calls the commands to process the XSLT transformation
?>
