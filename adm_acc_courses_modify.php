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
		$courses[0][0] = $_POST['account_id'];
		$courses[0][1] = $_POST['name'];
		$courses[0][2] = $_POST['comments'];
		$courses[0][3] = $_POST['code'];
		$courses[0][4] = $_POST['group'];
		$courses[0][5] = $_POST['semester'];
		$courses[0][6] = $_POST['year'];
		$courses[0][7] = $_POST['lecturer'];

		switch (User_Validate_Simple_Field($courses[0][1], 15))
		{
			case 0: $error[$num_errors++] = "The Name must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the name"; break;
		}

		switch (User_Validate_Simple_Field($rooms[0][2], 256))
		{
			case -2: $error[$num_errors++] = "There are invalid characteres at the comments"; break;
		}

		switch (User_Validate_Simple_Field($courses[0][3], 16))
		{
			case 0: $error[$num_errors++] = "The Code must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the Code"; break;
		}

		switch (User_Validate_Simple_Field($courses[0][4], 16))
		{
			case 0: $error[$num_errors++] = "The Group must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the Group"; break;
		}

		switch (User_Validate_Numeric_Field($courses[0][5]))
		{
			case -1: $error[$num_errors++] = "The year must be an integer value."; break;
			case 0: $error[$num_errors++] = "The year must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the year"; break;
		}

		switch (User_Validate_Numeric_Field($courses[0][6]))
		{
			case -1: $error[$num_errors++] = "The semester must be an integer value."; break;
			case 0: $error[$num_errors++] = "The semester must be filled"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the semester"; break;
		}

		switch (User_Validate_Simple_Field($courses[0][7], 32))
		{
			case 0: $error[$num_errors++] = "A Lecturer must be chosen"; break;
			case -2: $error[$num_errors++] = "There are invalid characteres at the Lecturer"; break;
		}

		$current = List_Courses('', '', $courses[0][3], $courses[0][4], '', $courses[5],
			$courses[0][6], $bd);
		if ($current)
			if ($current[0][0] != $courses[0][0])
				$error[$num_errors++] = 'This course already exists';

		if (empty($error))
		{
			Update_Course($courses[0][0], $courses[0][1], $courses[0][2], $courses[0][3],
				$courses[0][4], $courses[0][6], $courses[0][5], $courses[0][7], $bd);
			$alert[$num_alerts++] = "Course Updated Successfully";
			include "adm_acc_courses.php";
			exit;
		}
	}
	else
	{
		$courses = List_Courses($_GET['account_id'], '', '', '', '','', '', $bd);
	}

	$result_xsl = "xsl/" . $default_xsl . "/adm_acc_courses_modify.xsl";
	$category = List_Categories('', $cfg['professor_category'], $bd);
	$people = List_People('', '', '', '', $category[0][0], $bd);
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

$result_xml = $smarty->fetch("xml/adm_acc_courses.xml");

require("./inc/proc_transform.php");
// Calls the commands do procede the XSLT transformation
?>
