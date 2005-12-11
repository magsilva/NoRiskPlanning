<?php

// This module constains functions related to appointment handling

function List_Appointments($user, $is_group, $group_id, $authorized, $beg_day, $end_day, $beg_time, $end_time, $app_id, $owner, $app_times, $app_types,
	 $app_colors, $app_images, $days_of_week, $bd)
/* Returns an array with the common appointments of a user within
 * a given time span
 * The beginning and ending days must be informed in the format YYYY-MM-DD
*/
{
	if (!empty($beg_day))
		$condition .= " AND (date >= '$beg_day')";
	if (!empty($end_day))
		$condition .= " AND (date <= '$end_day')";
	if (!empty($app_id))
		$condition .= " AND (app_id = $app_id)";
	if (!empty($beg_time))
		$condition .= " AND (beg_time >= $beg_time)";
	if (!empty($end_time))
		$condition .= " AND (end_time <= $end_time)";
	if (!empty($owner))
		$condition .= " AND (owner = '$owner')";
	if (!empty($group_id))
		$condition .= " AND (group_id = '$group_id')";
	if (!empty($authorized) || $authorized == '0')
		$condition .= " AND (authorized = $authorized)";
	if (!empty($is_group) || $is_group == '0')
		$condition .= " AND (is_group_app = $is_group)";

	$query = "SELECT * FROM appointments WHERE (account_id = '$user')$condition ORDER BY beg_time";
	$result = $bd->Query($query);
	for ($i = 0; $i < $bd->NumberOfRows($result); $i++)
	{
		$apps[$i]['0']  = $bd->FetchResult($result, $i, 'app_id');
		$apps[$i]['1']  = 'common';
		$apps[$i]['2']  = $bd->FetchResult($result, $i, 'description');
		$group_id = $bd->FetchResult($result, $i, 'group_id');
		if ($bd->FetchResult($result, $i, 'is_group_app') == 1)
		{
			$query1 = "SELECT * FROM groups WHERE group_id = $group_id";
			$result1 = $bd->Query($query1);
			$group_acronynm = $bd->FetchResult($result1, 0, 'acronym');
			$apps[$i]['2'] .= " - ($group_acronynm)";
			$group_name = $bd->FetchResult($result1, 0, 'name');
		}
		$apps[$i]['7']  = $bd->FetchResult($result, $i, 'beg_time');
		$apps[$i]['9']  = $bd->FetchResult($result, $i, 'end_time');
		$apps[$i]['3']  = $apps[$i][9] - $apps[$i][7];
		$app_date     = $bd->FetchResult($result, $i, 'date');
		$app_date1 = explode("-", $app_date);
		$unix_time    = mktime(1, 1, 1, $app_date1[1], $app_date1[2], $app_date1[0]);
		$apps[$i]['4']  = $app_date[8] . $app_date[9];
		$apps[$i]['5']  = $app_date[5] . $app_date[6];
		$apps[$i]['6']  = $app_date[0] . $app_date[1] . $app_date[2] . $app_date[3];
		$apps[$i]['8']  = $app_times[$apps[$i][7]];
		$apps[$i]['10']  = $app_times[$apps[$i][9]];
		$type_number  = $bd->FetchResult($result, $i, 'type');
		$apps[$i]['11'] = $type_number;
		$apps[$i]['12']  = $app_types[$type_number];
		$apps[$i]['13'] = $app_colors[$type_number];
		$apps[$i]['14'] = $app_images[$type_number];
		$apps[$i]['15'] = date("w", $unix_time);
		$apps[$i]['16'] = $days_of_week[$apps[$i][15]];
		$apps[$i]['17'] = $bd->FetchResult($result, $i, 'url');
		$apps[$i]['18'] = $bd->FetchResult($result, $i, 'owner');
		$apps[$i]['19'] = $group_id;
		$apps[$i]['20'] = $group_name;
		$apps[$i]['21'] = $bd->FetchResult($result, $i, 'authorized');
		$apps[$i]['24'] = $bd->FetchResult($result, $i, 'room_id');
	}
	return $apps;
}

function List_Weekly_Appointments($user, $is_group, $group_id, $authorized, $beg_day, $end_day, $beg_time,
        $end_time, $app_id, $owner, $app_times, $app_types, $app_colors, $app_images, $days_of_week, $bd)
/* Returns an array with the weekly appointments of a user within
 * a given time span
 * The beginning and ending days must be informed in the YYYY-MM-DD format
*/
{

	if (!empty($beg_day) || $beg_day == '0')
		$condition .= " AND (day >= $beg_day)";
	if (!empty($end_day) || $end_day == '0')
		$condition .= " AND (day <= $end_day)";
	if (!empty($app_id))
		$condition .= " AND (app_id = $app_id)";
	if (!empty($beg_time))
		$condition .= " AND (beg_time >= $beg_time)";
	if (!empty($end_time))
		$condition .= " AND (end_time <= $end_time)";
	if (!empty($owner))
		$condition .= " AND (owner = '$owner')";
	if (!empty($group_id))
		$condition .= " AND (group_id = '$group_id')";
	if (!empty($authorized))
		$condition .= " AND (authorized = $authorized)";
	if (!empty($is_group) || $is_group == '0')
		$condition .= " AND (is_group_app = $is_group)";
	
	$query = "SELECT * FROM weekly_appointments WHERE (account_id = '$user')$condition ORDER BY beg_time";
	$result = $bd->Query($query);
	for ($i = 0; $i < $bd->NumberOfRows($result); $i++)
	{
		$apps[$i]['0']  = $bd->FetchResult($result, $i, 'app_id');
		$apps[$i]['1']  = 'weekly';
		$apps[$i]['2']  = $bd->FetchResult($result, $i, 'description');
		$group_id = $bd->FetchResult($result, $i, 'group_id');
		if ($bd->FetchResult($result, $i, 'is_group_app'))
		{
			$query1 = "SELECT * FROM groups WHERE group_id = $group_id";
			$result1 = $bd->Query($query1);
			$group_acronynm = $bd->FetchResult($result1, 0, 'acronym');
			$apps[$i]['2'] .= " - ($group_acronynm)";
			$group_name = $bd->FetchResult($result1, 0, 'name');
		}
		$apps[$i]['7']  = $bd->FetchResult($result, $i, 'beg_time');
		$apps[$i]['9']  = $bd->FetchResult($result, $i, 'end_time');
		$apps[$i]['3']  = $apps[$i][9] - $apps[$i][7];
		$apps[$i]['4']  = '';
		$apps[$i]['5'] = '';
		$apps[$i]['6']  = '';
		$apps[$i]['8']  = $app_times[$apps[$i][7]];
		$apps[$i]['10']  = $app_times[$apps[$i][9]];
		$type_number  = $bd->FetchResult($result, $i, 'type');
		$apps[$i]['11'] = $type_number;
		$apps[$i]['12']  = $app_types[$type_number];
		$apps[$i]['13'] = $app_colors[$type_number];
		$apps[$i]['14'] = $app_images[$type_number];
		$apps[$i]['15'] = $bd->FetchResult($result, $i, 'day');
		$apps[$i]['16'] = $days_of_week[$apps[$i][15]];
		$apps[$i]['17'] = $bd->FetchResult($result, $i, 'url');
		$apps[$i]['18'] = $bd->FetchResult($result, $i, 'owner');
		$apps[$i]['19'] = $group_id;
		$apps[$i]['20'] = $group_name;
		$apps[$i]['21'] = $bd->FetchResult($result, $i, 'authorized');
		$apps[$i]['24'] = $bd->FetchResult($result, $i, 'room_id');
	}
	return $apps;
}

function Get_Span_Limits($reference_day, $reference_month, $reference_year, $type_of_span, $mult_factor,
	&$span_start_day, &$span_start_month, &$span_start_year,
	&$span_end_day, &$span_end_month, &$span_end_year)
/* Returns the Span limits by a reference day, month and year
 * $type_of_span may be day, week, month or semester
*/
{
	switch($type_of_span)
	{
		case 'day':
			$unix_ref_day = mktime(5, 5, 5, $reference_month, $reference_day, $reference_year);
			$unix_ref_day =	$unix_ref_day + 24 * 60 * 60 * $mult_factor;
			$span_start_day = date('d', $unix_ref_day);
			$span_start_month = date('m', $unix_ref_day);
			$span_start_year = date('Y', $unix_ref_day);
			$span_end_day = $span_start_day;
			$span_end_month = $span_start_month;
			$span_end_year = $span_start_year;
		break;
		case 'week':
			$unix_ref_day = mktime(1, 1, 1, $reference_month, $reference_day, $reference_year);
			$unix_ref_day =	$unix_ref_day + 24 * 60 * 60 * 7 * $mult_factor;
			$day_of_week_ref = date('w', $unix_ref_day);
			$start_unix = $unix_ref_day - $day_of_week_ref * 24 * 60 * 60;
			// Sets the span start based on the reference day of week
			$end_unix = $start_unix + 6 * 24 * 60 * 60;
			// Sets the end start seven days after the start day
			$span_start_day = date('d', $start_unix);
			$span_start_month = date('m', $start_unix);
			$span_start_year = date('Y', $start_unix);
			$span_end_day = date('d', $end_unix);
			$span_end_month = date('m', $end_unix);
			$span_end_year = date('Y', $end_unix);
		break;
		case 'month':
			if ($mult_factor > 0)
				$num_days = 32;
			else
				$num_days = 28;
			$unix_ref_day = mktime(1, 1, 1, $reference_month, $reference_day, $reference_year);
			$unix_ref_day =	$unix_ref_day + 24 * 60 * 60 * $num_days * $mult_factor;
			$span_start_day = 1;
			$span_start_month = date('m', $unix_ref_day);
			$span_start_year = date('Y', $unix_ref_day);
			$span_end_day = date('t', $unix_ref_day);
			$span_end_month = $span_start_month;
			$span_end_year = $span_start_year;
		break;
	}
}

function Check_Appointment_Overlap($account_id, $day, $month, $year, $beg_time, $end_time, $excep_id, $bd)
/* Checks if there is one or more appointments within the given time span
 * Returns 1 if some appointment is found and 0 if not found
*/
{
	$date = $year . "-" . $month . "-" . $day;
	$query = "SELECT * FROM appointments WHERE (authorized=1) AND (date = '$date') AND (account_id = '$account_id')";
	if (!empty($excep_id))
		$query .= " AND (app_id <> $excep_id)";
	$query .= " AND (((beg_time >= $beg_time) AND (beg_time < $end_time)) OR
		((end_time <= $end_time) AND (end_time > $beg_time))
		OR (($beg_time >= beg_time) AND ($beg_time < end_time))
		OR (($end_time <= end_time) AND ($end_time > beg_time)))";
	$result = $bd->Query($query);
	if ($bd->NumberOfRows($result))
	// If some appointment was found
	{
		return $result;
	}
	else
		return 0;
}

function Check_Weekly_Appointment_Overlap($account_id, $dayofweek, $beg_time, $end_time, $excep_id, $bd)
/* Checks if there is one or more weekly appointments within the given time span
 * Returns 1 if some appointment is found and 0 if not found
*/
{
	$query = "SELECT * FROM weekly_appointments WHERE (authorized=1) AND (day = $dayofweek) AND (account_id = '$account_id')";
	if (!empty($excep_id))
		$query .= " AND (app_id <> $excep_id)";
	$query .= " AND (((beg_time >= $beg_time) AND (beg_time < $end_time)) OR
		((end_time <= $end_time) AND (end_time > $beg_time))
		OR (($beg_time >= beg_time) AND ($beg_time < end_time))
		OR (($end_time <= end_time) AND ($end_time > beg_time)))";
	$result = $bd->Query($query);
	if ($bd->NumberOfRows($result))
	// If some appointment was found
	{
		return $result;
	}
	else
		return 0;
}

function Delete_Appointment($account_id, $app_id, $bd)
{
	$query = "SELECT * FROM appointments WHERE (app_id = $app_id)";
	$result = $bd->Query($query);
	$room = $bd->FetchResult($result, 0, 'room_id');
	$date = $bd->FetchResult($result, 0, 'date');
	$beg_time = $bd->FetchResult($result, 0, 'beg_time');
	$end_time = $bd->FetchResult($result, 0, 'end_time');
	if ($room)
	{
		$query_room = "DELETE FROM appointments WHERE (account_id = '$room') AND
			(date = '$date') AND (beg_time = $beg_time) AND (end_time = $end_time)
			AND (owner = '$account_id')";
		$result = $bd->Query($query_room);
	}

	$query = "DELETE FROM appointments where (account_id = '$account_id') AND (app_id = '$app_id')";
	$result = $bd->Query($query);
}

function Clear_Schedule($account_id, $bd)
{
	$query = "DELETE FROM appointments where (account_id = '$account_id') AND (is_group = 0)";
	$result = $bd->Query($query);

	$query = "DELETE FROM weekly_appointments where (account_id = '$account_id') AND (is_group = 0)";
	$result = $bd->Query($query);
}


function Delete_Weekly_Appointment($account_id, $app_id, $bd)
{
	$query = "SELECT * FROM weekly_appointments WHERE (app_id = $app_id)";
	$result = $bd->Query($query);
	$room = $bd->FetchResult($result, 0, 'room_id');
	$day = $bd->FetchResult($result, 0, 'day');
	$beg_time = $bd->FetchResult($result, 0, 'beg_time');
	$end_time = $bd->FetchResult($result, 0, 'end_time');
	if ($room)
	{
		$query_room = "DELETE FROM appointments WHERE (account_id = '$room') AND
			(day = $day) AND (beg_time = $beg_time) AND (end_time = $end_time)
			AND (owner = '$account_id')";
		$result = $bd->Query($query_room);
	}

	$query = "DELETE FROM weekly_appointments where (account_id = '$account_id') AND (app_id = '$app_id')";
	$result = $bd->Query($query);
}

function Insert_Appointment($account_id, $description, $type, $date, $beg_time, $end_time, $url, $owner, $group_id, $is_group, $authorized, $bd)
{
	$query = "INSERT INTO appointments VALUES (NULL, '$account_id', '$description', $type, '$date',
		$beg_time, $end_time, '$url', '$owner', $group_id, $is_group, $authorized, '')";
	$result = $bd->Query($query);
}

function Insert_Weekly_Appointment($account_id, $description, $type, $day, $beg_time, $end_time, $url, $owner, $group_id, $is_group, $authorized,  $bd)
{
	$query = "INSERT INTO weekly_appointments VALUES (NULL, '$account_id', '$description', $type, $day,
		$beg_time, $end_time, '$url', '$owner', $group_id, $is_group, $authorized, '')";
	$result = $bd->Query($query);
}

function Update_Appointment($app_id, $description, $type, $date, $beg_time, $end_time, $url,
	$bd)
{
	$query = "UPDATE appointments SET ";
	if (!empty($description))
		$query .= "description = '$description' ";
	if (!empty($type) || $type == 0)
		$query .= ", type = $type ";
	if (!empty($day))
		$query .= ", date = '$date' ";
	if (!empty($beg_time) || $beg_time == 0)
		$query .= ", beg_time = $beg_time ";
	if (!empty($end_time))
		$query .= ", end_time = $end_time ";
	if (!empty($url))
		$query .= ", url = '$url' ";
	$query .= "WHERE app_id = $app_id";
	$result = $bd->Query($query);
}

function Update_Weekly_Appointment($app_id, $description, $type, $day, $beg_time, $end_time, $url, $bd)
{
	$query = "UPDATE weekly_appointments SET ";
	if (!empty($description))
		$query .= "description = '$description' ";
	if (!empty($type) || $type == 0)
		$query .= ", type = $type ";
	if (!empty($day) || $day == 0)
		$query .= ", day = '$day' ";
	if (!empty($beg_time) || $beg_time == 0)
		$query .= ", beg_time = $beg_time ";
	if (!empty($end_time))
		$query .= ", end_time = $end_time ";
	if (!empty($url))
		$query .= ", url = '$url' ";
	$query .= "WHERE app_id = $app_id";
	$result = $bd->Query($query);
}

function Is_In($wapp, $apps)
{
	$num_apps = count($apps);
	$found = 0;
	for ($i = 0; $i < $num_apps; $i++)
	{
		if ( (($wapp[7] < $apps[$i][7]) && ($wapp[9] > $apps[$i][7]))
			|| (($wapp[7] >= $apps[$i][7]) && ($wapp[9] <= $apps[$i][9]))
			|| (($wapp[7] < $apps[$i][9]) && ($wapp[9] > $apps[$i][9])) )
		{
			$found = 1;
		}
	}
	return $found;
}

function Comparison($a, $b)
{
	if ($a[7] == $b[7])
		return 0;
	else if ($a[7] > $b[7])
		return 1;
	else
		return -1;
}

function Retrieve_Appointments($user, $beg_date, $end_date, $beg_time, $end_time, $owner,
	$app_times, $app_types, $app_colors, $app_images, $days_of_week, $fake, $bd)
/* Return a User's appointments, ready to be printed
 * $days_of_weekMakes sure that no appointments are overlapping, and sets them
 * according to this priority scheme:
 * 1) Eventual appointments
 * 2) Weekly appointments
 */
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

	for ($date = $u_beg_date; $date <= $u_end_date; $date += 24 * 60 * 60)
	{
		$day = date('d', $date);
		$month = date('m', $date);
		$year = date('Y', $date);

		$w_date = date('w', $date);

		$f_date = date("Y-m-d", $date);

		$wapps = List_Weekly_Appointments($user, '', '', 1, $w_date, $w_date, $beg_time,
		    $end_time, '', $owner, $app_times, $app_types, $app_colors, $app_images, $days_of_week, $bd);

		$apps = List_Appointments($user, '', '', 1, $f_date, $f_date, $beg_time, $end_time, '', $owner,
			$app_times, $app_types, $app_colors, $app_images, $days_of_week, $bd);

		$num_wapps = count($wapps);

		for ($i = 0; $i < $num_wapps; $i++)
		{
			if (!Is_In($wapps[$i], $apps))
			{
				$wapps[$i][4] = $day;
				$wapps[$i][5] = $month;
				$wapps[$i][6] = $year;
				$apps[] = $wapps[$i];
			}
		}

		$num_times = count($app_times) - 1;

		if ($fake)
		{
			for ($i = 0; $i < $num_times; $i++)
			{
				$fake_app[7] = $i;
				$fake_app[9] = $i+1;
				if (!Is_In($fake_app, $apps))
				{
					$fake_app[3] = 1;
					$fake_app[4] = $day;
					$fake_app[5] = $month;
					$fake_app[6] = $year;
					$fake_app[1] = 'fake';
					$fake_app[8] = $app_times[$i];
					$fake_app[10] = $app_times[$i+1];
					$apps[] = $fake_app;
				}
			}
		}

		if ($apps)
			usort($apps, "Comparison");

		if (!$fake)
		// Sets the information about times before and after the appointment
		{
			for ($i = 0; $i < count($apps); $i++)
			{
				if ($i == 0)
					$apps[$i][22] = $apps[$i][7];
				else
					$apps[$i][22] = 0;
				/* Only the first appointment of the day will have a 'before' attribute
				else
					$apps[$i][22] = $apps[$i][7] - $apps[$i-1][9];*/
				if ($i == count($apps) - 1)
					$apps[$i][23] = count($app_times) - $apps[$i][9] - 1;
				else
					$apps[$i][23] = $apps[$i+1][7] - $apps[$i][9];
			}

		}

		for ($i = 0; $i < count($apps); $i++){
			$final_apps[] = $apps[$i];
		}
	}

	return $final_apps;
}

function Search_Appointments($account_id, $key, $app_times, $app_types, $app_colors, $app_images, $days_of_week, $bd)
// Returns a list of appointments which description contains the search key
{
	$query = "SELECT app_id FROM appointments WHERE
		account_id = '$account_id' AND ((description LIKE '$key')
		OR (description LIKE '%$key') OR (description LIKE '$key%')
		OR (description LIKE '%$key%'))";
	$result = $bd->Query($query);
	for ($i = 0; $i < $bd->NumberOfRows($result); $i++)
	{
		$app_id = $bd->FetchResult($result, $i, 'app_id');
		$app = List_Appointments($account_id, '', '', 1, '', '', '', '', $app_id, '',
			$app_times, $app_types, $app_colors, $app_images, $days_of_week, $bd);
		$final_apps[] = $app[0];
	}

	return $final_apps;
}

function Search_Weekly_Appointments($account_id, $key, $app_times, $app_types, $app_colors, $app_images, $days_of_week, $bd)
// Returns a list of weekly appointments which description contains the search key
{
	$query = "SELECT app_id FROM weekly_appointments WHERE
		account_id = '$account_id' AND (description LIKE '$key'
		OR description LIKE '%$key' OR description LIKE '$key%'
		OR description LIKE '%$key%')";
	$result = $bd->Query($query);
	for ($i = 0; $i < $bd->NumberOfRows($result); $i++)
	{
		$app_id = $bd->FetchResult($result, $i, 'app_id');
		$app = List_Weekly_Appointments($account_id, '', '', '', '', '', '', '', $app_id, '',
			$app_times, $app_types, $app_colors, $app_images, $days_of_week, $bd);
		$final_apps[] = $app[0];
	}

	return $final_apps;
}
?>
