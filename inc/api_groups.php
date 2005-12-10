<?

function Remove_Group($group_id, $cfg, $bd)
// Removes a entire group, all of its members, documents, notices and appointments
{
	$notices = "DELETE FROM group_notices WHERE group_id = $group_id";
	$bd->Query($notices);
	$doc_query = "SELECT * FROM group_docs WHERE group_id = $group_id";
	$result = $bd->Query($doc_query);
	for ($i = 0; $i < $bd->NumberOfRows($result); $i++)
	{
		$id = $bd->FetchResult($result, $i, 'doc_id');
		$name = $bd->FetchResult($result, $i, 'name');
		$path = $cfg['directory'].$cfg['docs_directory'].$id."_".$name;
		unlink($path);
	}
	$documents = "DELETE FROM group_docs WHERE group_id = $group_id";
	$bd->Query($documents);
	$members = "DELETE FROM group_members WHERE group_id = $group_id";
	$bd->Query($members);
	$appointments = "DELETE from appointments WHERE group_id = $group_id";
	$bd->Query($appointments);
	$week_appointments = "DELETE FROM weekly_appointments WHERE group_id = $group_id";
	$bd->Query($weekly_appointments);
	$group = "DELETE FROM groups WHERE group_id = $group_id";
	$bd->Query($group);
}

function List_Members($group_id, $enable_not_conf, $bd)
// Returns an array with the id and name of each member of a given group
{
	$query = "SELECT * FROM group_members WHERE group_id = $group_id";
	if (!$enable_not_conf)
		$query .= " AND membership <> 'I'";
	$result = $bd->Query($query);
	$members = array();
	for ($i = 0; $i < $bd->NumberOfRows($result); $i++)
	{
		$acc_id = $bd->FetchResult($result, $i, 'account_id');
		$members[$i][0] = $acc_id;
		$query1 = "SELECT name FROM accounts WHERE account_id = '$acc_id'";
		$result1 = $bd->Query($query1);
		$name = $bd->FetchResult($result1, 0, 'name');
		$members[$i][1] = $name;
		$members[$i][2] = $bd->FetchResult($result, $i, 'membership');
	}

	return $members;
}

function List_Group_Member($group_id, $member_id, $bd)
// Returns an array with the id and name of a specific member of a group
{
	$query = "SELECT * FROM group_members WHERE (group_id = $group_id) and (account_id = '$member_id')";
	$result = $bd->Query($query);
	$members = array();
	for ($i = 0; $i < $bd->NumberOfRows($result); $i++)
	{
		$acc_id = $bd->FetchResult($result, $i, 'account_id');
		$members[$i][0] = $acc_id;
		$query1 = "SELECT name FROM accounts WHERE account_id = '$acc_id'";
		$result1 = $bd->Query($query1);
		$name = $bd->FetchResult($result1, 0, 'name');
		$members[$i][1] = $name;
		$members[$i][2] = $bd->FetchResult($result, $i, 'membership');
	}

	return $members;
}

function List_Groups_Member($member, $enable_not_conf, $bd)
// Returns the list of groups
{
	$query = "SELECT groups.* FROM groups, group_members WHERE (group_members.account_id = '$member') AND
		(group_members.group_id = groups.group_id) GROUP BY groups.group_id";

	$result = $bd->Query($query);

	$groups = "";
	$num_groups = $bd->NumberOfRows($result);

	if ($num_groups)
	{
		for ($i = 0; $i < $num_groups; $i++)
		{
			$g_id = $bd->FetchResult($result, $i, 'group_id');
			$groups[$i][0] = $g_id;
			$groups[$i][1] = $bd->FetchResult($result, $i, 'name');
			$groups[$i][2] = $bd->FetchResult($result, $i, 'category');
			$groups[$i][3] = $bd->FetchResult($result, $i, 'acronym');
			$groups[$i][4] = $bd->FetchResult($result, $i, 'description');
			$groups[$i][5] = List_Members($g_id, $enable_not_conf, $bd);
		}
	}
	return $groups;
}

function List_Groups($group_id, $name, $category, $acronym, $enable_not_conf, $bd)
// Returns the list of groups
{
        if (!empty($group_id))
                $condition .= " AND (group_id=$group_id)";
        if (!empty($name))
                $condition .= " AND (name = '$name')";
        if (!empty($acronym))
                $condition .= " AND (acronym = '$acronym')";
        if (!empty($category))
                $condition .= " AND (category = '$category')";

	$query = "SELECT * FROM groups WHERE (group_id IS NOT NULL)$condition";
	$result = $bd->Query($query);

	$groups = "";
	$num_groups = $bd->NumberOfRows($result);

	if ($num_groups)
	{
		for ($i = 0; $i < $num_groups; $i++)
		{
			$g_id = $bd->FetchResult($result, $i, 'group_id');
			$groups[$i][0] = $g_id;
			$groups[$i][1] = $bd->FetchResult($result, $i, 'name');
			$groups[$i][2] = $bd->FetchResult($result, $i, 'category');
			$groups[$i][3] = $bd->FetchResult($result, $i, 'acronym');
			$groups[$i][4] = $bd->FetchResult($result, $i, 'description');
			$groups[$i][5] = List_Members($g_id, $enable_not_conf, $bd);
		}
	}

	return $groups;
}

Function Insert_Group($name, $category, $acronym, $description, $bd)
// Inserts a new group
{
	$query = "INSERT INTO groups VALUES (NULL, '$category', '$name', '$acronym', '$description')";
	$bd->Query($query);
}

Function Insert_Group_Member($group_id, $account_id, $membership, $conf_code, $bd)
// Inserts a new group member
{
	$query = "INSERT INTO group_members VALUES ('$group_id', '$account_id', '$membership', '$conf_code')";
	$bd->Query($query);
}

Function Member_Role($group_id, $account_id, $bd)
// Returns the membership role of a group's member.
// Returns -1 if the user is not member of the group
{
	$query = "SELECT * FROM group_members WHERE (account_id = '$account_id') and (group_id = $group_id)";
	$result = $bd->Query($query);
	if ($bd->NumberOfRows($result) > 0)
	{
		return $bd->FetchResult($result, 0, 'membership');
	}
	else
		return -1;
}

function Group_Insert_Member($group_id, $member_id, $membership, $conf_code, $bd)
// Inserts a new member into a group
{
	$query = "INSERT INTO group_members VALUES ($group_id, '$member_id', '$membership', '$conf_code')";
	$bd->Query($query);
}

function Group_Remove_Member($group_id, $member_id, $bd)
// Removes a member from a group, as long as all information related to him and the group
{
	$query = "DELETE FROM group_members WHERE (group_id = $group_id) and
		(account_id = '$member_id')";
	$bd->Query($query);
	$query = "DELETE FROM group_documents WHERE (group_id = $group_id) and
		(account_id = '$member_id')";
	$bd->Query($query);
	$query = "DELETE FROM group_notices WHERE (group_id = $group_id) and
		(account_id = '$member_id')";
	$bd->Query($query);
	$query = "DELETE FROM appointments WHERE (group_id = $group_id) and
		(account_id = '$member_id')";
	$bd->Query($query);
	$query = "DELETE FROM weekly_appointments WHERE (group_id = $group_id) and
		(account_id = '$member_id')";
	$bd->Query($query);
}

function Group_Change_Membership($group_id, $member_id, $membership, $bd)
// Changes the membership of a given user in a group
{
	$query = "UPDATE group_members SET membership='$membership' WHERE
		(group_id = $group_id) and (account_id = '$member_id')";
	$bd->Query($query);
}

function Get_Confirmation_Code($group_id, $member_id, $bd)
// Gets the confirmation code of a given member of group
{
	$query = "SELECT * FROM group_members WHERE (group_id = $group_id) AND (account_id = '$member_id')";
	$result = $bd->Query($query);
	if ($bd->NumberOfRows($result))
	{
		$conf_code = $bd->FetchResult($result, 0, 'confirm_code');
		return $conf_code;
	}
	else
		return '';
}

function Insert_Notice($group_id, $account_id, $text, $bd)
// Inserts a new notice to a group
{
	$query = "INSERT INTO group_notices VALUES($group_id, '$account_id', NULL, now(), now(), '$text')";
	$bd->Query($query);
}

function List_Notices($group_id, $notice_id, $number, $bd)
// Returns an array with the notices information
{
	if (!empty($group_id))
		$query = "SELECT * FROM group_notices WHERE group_id = $group_id";
	else
		$query = "SELECT * FROM group_notices WHERE notice_id = $notice_id";
	if (!empty($number))
		$query .= " ORDER BY date DESC, time LIMIT 0, $number";
	$result = $bd->Query($query);
	$notices = array();
	for ($i = 0; $i < $bd->NumberOfRows($result); $i++)
	{
		$notices[$i][0] = $bd->FetchResult($result, $i, 'notice_id');
		$notices[$i][1] = $bd->FetchResult($result, $i, 'group_id');
		$notices[$i][2] = $bd->FetchResult($result, $i, 'account_id');
		$notices[$i][3] = $bd->FetchResult($result, $i, 'description');
		$date = $bd->FetchResult($result, $i, 'date');
		$notices[$i][4] = substr($date, 5, 2) . "/" . substr($date, 8, 2) . "/" . substr($date, 0, 4);
		$time = $bd->FetchResult($result, $i, 'time');
		$notices[$i][5] = substr($time, 0, 5);
	}

	return $notices;
}



function Remove_Notice($notice_id, $bd)
// Removes a group notice
{
	$query = "DELETE FROM group_notices where notice_id = $notice_id";
	$bd->Query($query);
}

function List_Documents($group_id, $doc_id, $cfg, $bd)
// Returns an array with documents information
{
	if (!empty($group_id))
		$query = "SELECT * FROM group_docs WHERE group_id = $group_id";
	else
		$query = "SELECT * FROM group_docs WHERE doc_id = $doc_id";
	$result = $bd->Query($query);
	$documents = array();
	for ($i = 0; $i < $bd->NumberOfRows($result); $i++)
	{
		$documents[$i][0] = $bd->FetchResult($result, $i, 'doc_id');
		$documents[$i][1] = $bd->FetchResult($result, $i, 'group_id');
		$documents[$i][2] = $bd->FetchResult($result, $i, 'account_id');
		$documents[$i][3] = $cfg['url'].$cfg['docs_directory'].
			$documents[$i][0]."_".$bd->FetchResult($result, $i, 'name');
		$documents[$i][4] = $bd->FetchResult($result, $i, 'size');
		$documents[$i][5] = $bd->FetchResult($result, $i, 'description');
		$documents[$i][6] = $bd->FetchResult($result, $i, 'name');
	}

	return $documents;
}

function Insert_Document($group_id, $account_id, $size, $name, $description, $bd)
// Inserts a new document to a group
{
	$query = "INSERT INTO group_docs VALUES(NULL, '$account_id', $group_id, $size, '$name', '$description')";
	$bd->Query($query);
	$query1 = "SELECT * FROM group_docs WHERE (account_id = '$account_id') AND (group_id = $group_id) AND
		(size = $size) AND (name='$name') AND (description = '$description')";
	$result = $bd->Query($query1);
	$id = $bd->FetchResult($result, 0, 'doc_id');
	return $id;
}

function Remove_Document($doc_id, $bd)
// Removes a group document
{
	$query = "DELETE FROM group_docs where doc_id = $doc_id";
	$bd->Query($query);
}


function Retrieve_Appointments_Groups($group, $beg_date, $end_date, $beg_time, $end_time,
	$app_times, $app_types, $app_colors, $app_images, $days_of_week, $fake, $bd)
// Returns an array with the appointments of all members of a given group within a time span
{
	$members = List_Members($group, 0, $bd);
	$apps = array();
	for ($i = 0; $i < count($members); $i++)
	{
		$user = $members[$i][0];
		$apps[$i] = Retrieve_Appointments($user, $beg_date, $end_date, $beg_time, $end_time, '',
			$app_times, $app_types, $app_colors, $app_images, $days_of_week, $fake, $bd);

	}
	return $apps;
}

function Group_Free_Schedule($group, $beg_date, $end_date, $beg_time, $end_time,
	$app_times, $days_of_week, $bd)
// Returns a list with common times free to all members of the group
{
	$beg_month = substr($beg_date, 5, 2);
	$beg_year = substr($beg_date, 0, 4);
	$beg_day = substr($beg_date, 8, 2);
	$end_month = substr($end_date, 5, 2);
	$end_year = substr($end_date, 0, 4);
	$end_day = substr($end_date, 8, 2);

	$u_beg_date = mktime(1, 1, 1, $beg_month, $beg_day, $beg_year);
	$u_end_date = mktime(1, 1, 1, $end_month, $end_day, $end_year);

	$num_days = ($u_end_date - $u_beg_date) / (24 * 60 * 60) + 1;

	$k = 0;
	for ($date = $u_beg_date; $date <= $u_end_date; $date += 24 * 60 * 60)
	{
		$day = date('d', $date);
		$month = date('m', $date);
		$year = date('Y', $date);

		$w_date = date('w', $date);

		$f_date = date("Y-m-d", $date);

		for ($i = 0; $i < (count($app_times) - 1); $i++)
		{
			$overlap = Group_Check_Appointment_Overlap($group, $day, $month, $year,
				$i, $i+1, $bd);
			$overlap_w = Group_Check_Weekly_Appointment_Overlap($group, $w_date,
				$i, $i+1, $bd);

			$free = !($overlap || $overlap_w);

			$apps[$k][0] = '';
			$apps[$k][0] = 'common';
			$apps[$k][11] = 0;
			if ($free)
				$apps[$k][12] = 'free';
			else
				$apps[$k][12] = 'notfree';
			$apps[$k][7] = $i;
			$apps[$k][9] = $i+1;
			$apps[$k][3] = 1;
			$apps[$k][4] = $day;
			$apps[$k][5] = $month;
			$apps[$k][6] = $year;
			$apps[$k][8] = $app_times[$i];
			$apps[$k][10] = $app_times[$i+1];
			$k++;
		}
	}

	return $apps;
}

function Group_Check_Appointment_Overlap($group_id, $day, $month, $year, $beg_time, $end_time, $bd)
{
	$members = List_Members($group_id, 0, $bd);

	$overlap = 0;
	for ($j = 0; ($j < count($members)) && (!$overlap); $j++)
	{
		$current = Check_Appointment_Overlap($members[$j][0], $day, $month, $year,
			$beg_time, $end_time, '', $bd);
		if ($current == 0)
			$overlap = 0;
		else
			$overlap = 1;
	}

	return ($overlap);
}

function Group_Check_Weekly_Appointment_Overlap($group_id, $dayofweek, $beg_time, $end_time, $bd)
{
	$members = List_Members($group_id, 0, $bd);

	$overlap = 0;
	for ($j = 0; ($j < count($members)) && !$overlap; $j++)
	{
		$current_w = Check_Weekly_Appointment_Overlap($members[$j][0], $dayofweek,
			$beg_time, $end_time, '', $bd);
		if ($current_w == 0)
			$overlap = 0;
		else
			$overlap = 1;
	}

	return ($overlap);
}

function Group_Insert_Appointment($group_id, $description, $type, $date, $beg_time, $end_time, $url, $owner, $bd)
// Inserts an appointment to all members of a group
{
	$members = List_Members($group_id, 0, $bd);

	for ($i = 0; $i < count($members); $i++)
	{
		Insert_Appointment($members[$i][0], $description, $type, $date, $beg_time, $end_time, $url,
			$owner, $group_id, 1, 0, $bd);
	}
}

function Group_Insert_Weekly_Appointment($group_id, $description, $type, $day, $beg_time, $end_time, $url, $owner, $bd)
// Inserts a weekly appointment to all members of a group
{
	$members = List_Members($group_id, 0, $bd);

	for ($i = 0; $i < count($members); $i++)
	{

		Insert_Weekly_Appointment($members[$i][0], $description, $type, $day, $beg_time,
			$end_time, $url, $owner, $group_id, 1, 0,  $bd);
	}
}

function Group_Delete_Appointment($ref_app_id, $account_id, $bd)
// Deletes a group apppointment, given a reference appointment of one of the members
{
	$ref_app = List_Appointments($account_id, 1, '', '', '', '', '', '', $ref_app_id, '', '', '', '', '', '', $bd);
	$beg_time = $ref_app[0][7];
	$end_time = $ref_app[0][9];
	$date = $ref_app[0][6] . "-" . $ref_app[0][5] . "-" . $ref_app[0][4];
	$group_id = $ref_app[0][19];
	$query = "DELETE FROM appointments WHERE (is_group_app = 1) AND (beg_time = $beg_time)
		AND (end_time = $end_time) AND (date = '$date') AND (group_id = $group_id)";
	$bd->Query($query);
}

function Group_Delete_Weekly_Appointment($ref_app_id, $account_id, $bd)
// Deletes a group apppointment, given a reference appointment of one of the members
{
	$ref_app = List_Weekly_Appointments($account_id, 1, '', '', '', '', '', '', $ref_app_id, '', '', '', '', '', '', $bd);
	$beg_time = $ref_app[0][7];
	$end_time = $ref_app[0][9];
	$day = $ref_app[0][15];
	$group_id = $ref_app[0][19];
	$query = "DELETE FROM weekly_appointments WHERE (is_group_app = 1) AND (beg_time = $beg_time)
		AND (end_time = $end_time) AND (day = $day) AND (group_id = $group_id)";
	$bd->Query($query);
}


?>
