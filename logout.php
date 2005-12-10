<?php
if ($already_initialized != 1)
{
	require("./inc/script_inicialization.php");
}

if (!isset($num_errors))
	$num_errors = 0;
if (!isset($num_alerts))
	$num_alerts = 0;

$master_session = Get_Master_Session($sess_id, $bd);

if ($master_session)
/* If master_session is not the current session
 * And the current session was started from another one 
 */
{
	$m_complete_sess_id = md5($master_session);
	$m_complete_sess_id .= $master_session;
	Terminate_Session($sess_id, 'logout', $bd);
	header("location: main.php?sess_id=$m_complete_sess_id");
	exit;
}

if (Terminate_Session($sess_id, 'logout', $bd))
	$alert[$num_alerts++] = "System logout successful!";
else
{
	$error[$num_alerts++] = "Invalid session ID!";
}

$result_xsl = "xsl/" . $default_xsl . "/logout.xsl";

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpIndexAlerts", $alert);
$smarty->assign("nrpIndexErrors", $error);

$result_xml = $smarty->fetch("xml/index.xml");

       require("./inc/proc_transform.php");
?>
