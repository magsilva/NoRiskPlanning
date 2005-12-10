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

if (Validate_Session($complete_sess_id, $_SERVER['REMOTE_ADDR'], $bd))
{
	Get_Account_Id($sess_id, $account_id, $bd);
	if ($account_id != 'admin')
	{
		$error[$num_errors++] = "You are not the administrator";
		include "logout.php";
		exit;
	}

	if (!empty($_POST['modify']))
	// If the submit button to create the unit was pressed
	{
		$people[0][0] = $_POST['account_id'];
		$people[0][1] = $_POST['name'];
		$people[0][2] = $_POST['comments'];
		$people[0][3] = $_POST['dep_id'];
		$deps = List_Departments($people[0][3], '', '', $bd);
		$people[0][4] = $deps[0][1];
		$people[0][5] = $_POST['email'];
		$people[0][6] = $_POST['url'];
		$people[0][8] = $_POST['category'];
		$categ = List_Categories($people[0][8], '', $bd);
		$people[0][9] = $categ[0][1];

		$cur_person = List_People($people[0][0], '', '', '', '', $bd);
		$cur_email = $cur_person[0][5];

		switch (User_Validate_Simple_Field($people[0][1], 15))
		{
			case 0: $error[$num_errors++] = "The Name must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the name"; break;
		}

		switch (User_Validate_Simple_Field($people[0][2], 256))
		{
			case -2: $error[$num_errors++] = "There are invalid characteres at the comments"; break;
		}

		switch (User_Validate_Simple_Field($people[0][5], 100))
		{
			case 0: $error[$num_errors++] = "The E-mail must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the e-mail"; break;
			case 1:{
				$current = List_People('', '', $people[0][5], '', '', $bd);
				if ($current)
					if ($current[0][5] != $cur_email)
						$error[$num_errors++] = "This e-mail is already in use";
			}break;
		}

		switch (User_Validate_Simple_Field($people[0][6], 100))
		{
			case -2: $error[$num_errors++] = "There are invalid characteres at the url"; break;
		}

		if (empty($error))
		{
			$comp_person = List_People($people[0][0], '', '', '', '', $bd);
			Update_Person($people[0][0], $people[0][1], $people[0][2], $people[0][5], 
				$people[0][3], $people[0][6], $comp_person[0][7], $people[0][8], $bd);
			$alert[$num_alerts++] = "Person Updated Successfully";
			include "adm_acc_people.php";
			exit;
		}
	}
	else
	{
		$people = List_People($_GET['account_id'], '', '', '', '', $bd);
	}

	$departments = List_Departments('', '', '', $bd);
	$categories = List_Categories('', '', $bd);

	$result_xsl = "xsl/" . $default_xsl . "/adm_acc_people_modify.xsl";
}
else
{
	$error[$num_errors++] = "Invalid Session ID";
	include "logout.php";
	exit;
}

$smarty->assign("nrpTransform", $result_xsl);
$smarty->assign("nrpSchErrors", $error);
$smarty->assign("nrpSchAlerts", $alert);
$smarty->assign("nrpSessId", $complete_sess_id);
$smarty->assign("nrpUserId", $account_id);
$smarty->assign("nrpPeople", $people);
$smarty->assign("nrpCategories", $categories);
$smarty->assign("nrpDepartments", $departments);

$result_xml = $smarty->fetch("xml/adm_acc_people.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
