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

	if (!empty($_POST['create']))
	// If the submit button to create the unit was pressed
	{
		$permissions[0][0] = '';
		$permissions[0][1] = $_POST['master_person'];
		$person = List_People($permissions[0][1], '', '', '', '', $bd);
		$permissions[0][2] = $person[0][1];
		$permissions[0][3] = $_POST['master_group'];
		$group = List_Groups($permissions[0][3], '', '', '', 0, $bd);
		$permissions[0][4] = $group[0][1];
		$permissions[0][5] = $_POST['master_category'];
		$category = List_Categories($permissions[0][5], '', $bd);
		$permissions[0][6] = $category[0][1];
		$permissions[0][7] = $_POST['slave_id'];
		$course = List_Courses($permissions[0][7], '', '', '', '', '', '', $bd);
		$permissions[0][8] = $course[0][1];
		$permissions[0][9] = 'course';

		switch (User_Validate_Simple_Field($permissions[0][1], 32))
		{
			case 0: $person_ok = 0; break;
			case -2: $person_ok = 0; break;
			case 1: $person_ok = 1; break;
		}

		switch (User_Validate_Simple_Field($permissions[0][3], 32))
		{
			case 0: $group_ok = 0; break;
			case -2: $group_ok = 0; break;
			case 1: $group_ok = 1; break;
		}

		switch (User_Validate_Simple_Field($permissions[0][5], 32))
		{
			case 0: $category_ok = 0; break;
			case -2: $category_ok = 0; break;
			case 1: $category_ok = 1; break;
		}

		switch (User_Validate_Simple_Field($permissions[0][7], 16))
		{
			case 0: $error[$num_errors++] = "A Slave Course must be chosen"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the slave id"; break;
		}

		if (($permissions[0][1] xor $permissions[0][3]) xor ($permissions[0][5]))
		// If only one of the options was chosen
		{
			$current = List_Permissions('', $permissions[0][1], $permissions[0][3],
				$permissions[0][5], $permissions[0][7], 'course', $bd);
			if ($current)
				$error[$num_errors++] = 'This permition already exists';
		}
		else
		{
			$error[$num_errors++] = "One (and only one) option of master must be chosen";
		}

		if (empty($error))
		{

			Insert_Permission($permissions[0][1], $permissions[0][3], $permissions[0][5],
				$permissions[0][7], 'course', $bd);
			$alert[$num_alerts++] = "New Permision to Course Inserted Successfully";
			include "adm_permissions.php";
			exit;
		}
	}

	$result_xsl = "xsl/" . $default_xsl . "/adm_permissions_course_new.xsl";

	$categories = List_Categories('', '', $bd);
	$people = List_People('', '', '', '', '', $bd);
	$groups = List_Groups('', '', '', '', '', $bd);
	$courses = List_Courses('', '', '', '', '', '', '', $bd);
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
$smarty->assign("nrpCourses", $courses);
$smarty->assign("nrpPeople", $people);
$smarty->assign("nrpCategories", $categories);
$smarty->assign("nrpGroups", $groups);
$smarty->assign("nrpPermissions", $permissions);

$result_xml = $smarty->fetch("xml/adm_permissions.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
