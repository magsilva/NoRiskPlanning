<?php

function List_Units($unit_id, $unit_name,  $bd)
// Returns the list of units
{

	if (!empty($unit_id))
		$condition = "unit_id=$unit_id";
	if (!empty($unit_name))
	{
		if (!empty($condition))
			$condition .= " AND ";
		$condition .= "name = '$unit_name'";
	}
	if (!empty($condition))
		$condition = "WHERE " . $condition;

	$query = "SELECT * FROM units $condition";
	$result = $bd->Query($query);

	$units = "";
	$num_units = $bd->NumberOfRows($result);

	if ($num_units)
	{
		for ($i = 0; $i < $num_units; $i++)
		{
			$units[$i][0] = $bd->FetchResult($result, $i, 'unit_id');
			$units[$i][1] = $bd->FetchResult($result, $i, 'name');
			$units[$i][2] = $bd->FetchResult($result, $i, 'acronym');
			$units[$i][3] = $bd->FetchResult($result, $i, 'description');
		}
	}
	return $units;
}

function List_Departments($dep_id, $dep_name, $unit_id, $bd)
/* Returns the list of departments of a given unit,
 * a specific department, or all the departments
 */
{
	$condition = "";
	if (!empty($dep_id))
		$condition .= "dep_id = $dep_id";
	if (!empty($unit_id))
	{
		if (!empty($condition))
			$condition .= " AND ";
		$condition .= "unit_id = $unit_id";
	}
	if (!empty($dep_name))
	{
		if (!empty($condition))
			$condition .= " AND ";
		$condition .= "name = '$dep_name'";
	}
	if (!empty($condition))
		$condition = " WHERE " . $condition;
	$query = "SELECT * FROM departments $condition";
	$result = $bd->Query($query);
	$num_dep = $bd->NumberOfRows($result);
	$dep = "";
	if ($num_dep)
	{
		for ($i = 0; $i < $num_dep; $i++)
		{
			$dep[$i][0] = $bd->FetchResult($result, $i, 'dep_id');
			$dep[$i][1] = $bd->FetchResult($result, $i, 'name');
			$dep[$i][2] = $bd->FetchResult($result, $i, 'acronym');
			$dep[$i][3] = $bd->FetchResult($result, $i, 'description');
			$dep[$i][4] = $bd->FetchResult($result, $i, 'unit_id');
			$unit = $dep[$i][4];
			$query1 = "SELECT * FROM units WHERE unit_id = $unit";
			$result1 = $bd->Query($query1);
			$dep[$i][5] = $bd->FetchResult($result1, 0, 'name');
		}
	}
	return $dep;
}

function Delete_unit($unit_id, $bd)
// Deletes a given unit
{
	$deps = List_Departments('', '', $unit_id, $bd);
	if ($deps)
	// If there are departments linked to this unit
	{
		return 0;
	}
	else
	{
		$query = "DELETE FROM units WHERE unit_id = $unit_id";
		$result = $bd->Query($query);
		return 1;
	}
}

function Insert_Unit($unit_name, $unit_acronym, $unit_description, $bd)
// Inserts a new unit
{
	$query = "INSERT INTO units VALUES (NULL, '$unit_name', '$unit_acronym', '$unit_description')";
	$bd->Query($query);
}

function Update_Unit($unit_id, $unit_name, $unit_acronym, $unit_description, $bd)
// Updates a unit
{
	$query = "UPDATE units SET name='$unit_name', acronym='$unit_acronym', description = '$unit_description'
		WHERE unit_id = $unit_id";
	$bd->Query($query);
}

function Insert_Department($dep_name, $dep_acronym, $dep_description, $unit_id, $bd)
// Inserts a new department
{
	$query = "INSERT INTO departments VALUES (NULL, $unit_id, '$dep_name', '$dep_acronym', '$dep_description')";
	$bd->Query($query);
}

function Delete_Department($dep_id, $bd)
// Deletes a given department
{
	$query = "SELECT * FROM people WHERE dep_id = $dep_id";
	$result = $bd->Query($query);
	if ($bd->NumberOfRows($result) > 0)
	// If there are users linked to this department
	{
		return 0;
	}
	else
	{
		$query = "DELETE FROM departments WHERE dep_id = $dep_id";
		$result = $bd->Query($query);
		return 1;
	}
}

function Update_Department($dep_id, $dep_name, $dep_acronym, $dep_description, $unit_id, $bd)
// Updates a department
{
	$query = "UPDATE departments SET name='$dep_name', acronym='$dep_acronym',
		description = '$dep_description', unit_id = $unit_id WHERE dep_id = $dep_id";
	$bd->Query($query);
}

function List_Categories($cat_id, $cat_name,  $bd)
// Returns the list of categories
{
	if (!empty($cat_id))
		$condition = "cat_id=$cat_id";
	if (!empty($cat_name))
	{
		if (!empty($condition))
			$condition .= " AND ";
		$condition .= "name = '$cat_name'";
	}
	if (!empty($condition))
		$condition = "WHERE " . $condition;

	$query = "SELECT * FROM categories $condition";

	$result = $bd->Query($query);

	$cats = "";
	$num_cats = $bd->NumberOfRows($result);

	if ($num_cats)
	{
		for ($i = 0; $i < $num_cats; $i++)
		{
			$cats[$i][0] = $bd->FetchResult($result, $i, 'cat_id');
			$cats[$i][1] = $bd->FetchResult($result, $i, 'name');
			$cats[$i][2] = $bd->FetchResult($result, $i, 'description');
		}
	}
	return $cats;
}

function Insert_Category($cat_name, $cat_description,  $bd)
// Inserts a new category
{
	$query = "INSERT INTO categories VALUES (NULL, '$cat_name', '$cat_description')";
	$bd->Query($query);
}

function Delete_Category($cat_id, $bd)
// Deletes a given category
{
	$query = "SELECT * FROM people WHERE category = '$cat_id'";
	$result = $bd->Query($query);
	if ($bd->NumberOfRows($result) > 0)
	// If there are users linked to this category
	{
		return 0;
	}
	else
	{
		$query = "DELETE FROM categories WHERE cat_id = $cat_id";
		$result = $bd->Query($query);
		return 1;
	}
}

function Update_Category($cat_id, $cat_name, $cat_description, $bd)
// Updates a category
{
	$query = "UPDATE categories SET name='$cat_name', description = '$cat_description'
		WHERE cat_id = $cat_id";
	$bd->Query($query);
}

function Check_Account_Id($account_id, $bd)
//Checks whether a given account_id exists or not
{
	$query = "SELECT * FROM accounts WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	if ($bd->NumberOfRows($result))
		return 1;
	else
		return 0;
}

function List_People($account_id, $name, $email, $dep_id, $category, $bd)
// Returns the list of people
{
	if (!empty($account_id))
		$condition .= " AND (accounts.account_id='$account_id')";
	if (!empty($name))
		$condition .= " AND (name = '$name')";
	if (!empty($email))
		$condition .= " AND (email = '$email')";
	if (!empty($dep_id))
		$condition .= " AND (dep_id = $dep_id)";
	if (!empty($category))
		$condition .= " AND (category = '$category')";

	$query = "SELECT * FROM accounts, people WHERE (accounts.account_id = people.account_id) AND
		(accounts.account_id != 'admin')$condition";

	$result = $bd->Query($query);

	$people = "";
	$num_people = $bd->NumberOfRows($result);

	if ($num_people)
	{
		for ($i = 0; $i < $num_people; $i++)
		{
			$people[$i][0] = $bd->FetchResult($result, $i, 'account_id');
			$people[$i][1] = $bd->FetchResult($result, $i, 'name');
			$people[$i][2] = $bd->FetchResult($result, $i, 'commentaries');
			$people[$i][3] = $bd->FetchResult($result, $i, 'dep_id');
			$dep_id = $people[$i][3];
			$query1 = "SELECT * FROM departments WHERE dep_id = $dep_id";
			$result1 = $bd->Query($query1);
			$people[$i][4] = $bd->FetchResult($result1, 0, 'name');
			$people[$i][5] = $bd->FetchResult($result, $i, 'email');
			$people[$i][6] = $bd->FetchResult($result, $i, 'url');
			$people[$i][7] = $bd->FetchResult($result, $i, 'public_types');
			$people[$i][8] = $bd->FetchResult($result, $i, 'category');
			$category = $people[$i][8];
			$query1 = "SELECT * FROM categories WHERE cat_id = $category";
			$result1 = $bd->Query($query1);
			$people[$i][9] = $bd->FetchResult($result1, 0, 'name');
		}
	}
	return $people;
}

function Insert_Person($account_id, $name, $comments, $email, $dep_id, $url, $password, $category, 
	$public_types, $bd)
// Inserts a new person
{
	$query = "INSERT INTO accounts VALUES ('$account_id', 'user', '$name', '$comments')";
	$bd->Query($query);
	$query = "INSERT INTO people VALUES ('$account_id', '$email', $dep_id, '$url', '$password',
		'$public_types', $category)";
	$bd->Query($query);
}

function Delete_Person($account_id, $bd)
// Deletes a given user, and all its dependencies
{

	$query = "DELETE FROM accounts WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	$query = "DELETE FROM people WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	$query = "DELETE FROM appointments WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	$query = "DELETE FROM weekly_appointments WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	/*
	Include commands to delete group related information!!!!!!!
	*/

	return 1;
}

function Update_Person($account_id, $name, $comments, $email, $dep_id, $url, $public_types, $category, $bd)
// Updates a category
{
	$query = "UPDATE accounts SET name='$name', commentaries = '$comments' WHERE account_id = '$account_id'";
	$bd->Query($query);
	$query1= "UPDATE people SET email='$email', dep_id=$dep_id, url='$url', public_types='$public_types',
		category='$category' WHERE account_id = '$account_id'";
	$bd->Query($query1);
}

function Admin_Set_Password($current, $new_password, $bd)
/* Sets a new password for administrator,
 * Returns 1 if OK, and 0 if the current password is wront
*/
{
	$current_md5 = md5($current);
	$new_md5 = md5($new_password);

	$query = "SELECT password FROM people WHERE account_id = 'admin'";
	$result = $bd->Query($query);
	$actual_password = $bd->FetchResult($result, 0, 'password');
	if ($actual_password == $current_md5)
	{
		$query = "UPDATE people SET password = '$new_md5' WHERE account_id = 'admin'";
		$bd->Query($query);
		return 1;
	}
	else
	{
		return 0;
	}
}

function List_Rooms($account_id, $name, $code, $capacity, $type, $bd)
// Returns the list of rooms
{
	if (!empty($account_id))
		$condition .= " AND (accounts.account_id='$account_id')";
	if (!empty($name))
		$condition .= " AND (name = '$name')";
	if (!empty($code))
		$condition .= " AND (code = '$code')";
	if (!empty($capacity))
		$condition .= " AND (capacity = $capacity)";
	if (!empty($type))
		$condition .= " AND (type = '$type')";

	$query = "SELECT * FROM accounts, rooms WHERE (accounts.account_id = rooms.account_id)$condition";

	$result = $bd->Query($query);

	$rooms = "";
	$num_rooms = $bd->NumberOfRows($result);

	if ($num_rooms)
	{
		for ($i = 0; $i < $num_rooms; $i++)
		{
			$rooms[$i][0] = $bd->FetchResult($result, $i, 'account_id');
			$rooms[$i][1] = $bd->FetchResult($result, $i, 'name');
			$rooms[$i][2] = $bd->FetchResult($result, $i, 'commentaries');
			$rooms[$i][3] = $bd->FetchResult($result, $i, 'code');
			$rooms[$i][4] = $bd->FetchResult($result, $i, 'capacity');
			$rooms[$i][5] = $bd->FetchResult($result, $i, 'location');
			$rooms[$i][6] = $bd->FetchResult($result, $i, 'type');
		}
	}
	return $rooms;
}

function Insert_Room($account_id, $name, $comments, $code, $capacity, $location, $type, $bd)
// Inserts a new room
{
	$query = "INSERT INTO accounts VALUES ('$account_id', 'room', '$name', '$comments')";
	$bd->Query($query);
	$query = "INSERT INTO rooms VALUES ('$account_id', '$code', $capacity, '$location', '$type')";
	$bd->Query($query);
}

function Delete_Room($account_id, $bd)
// Deletes a given room, and all its dependencies
{
	$query = "DELETE FROM accounts WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	$query = "DELETE FROM rooms WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	$query = "DELETE FROM appointments WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	$query = "DELETE FROM weekly_appointments WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	/*
	Include commands to delete group related information!!!!!!!
	*/
	return 1;
}

function Update_Room($account_id, $name, $comments, $code, $capacity, $location, $type, $bd)
// Updates a room
{
	$query = "UPDATE accounts SET name='$name', commentaries = '$comments' WHERE account_id = '$account_id'";
	$bd->Query($query);
	$query1= "UPDATE rooms SET code='$code', capacity=$capacity, location='$location', type='$type'
		 WHERE account_id = '$account_id'";
	$bd->Query($query1);
}

function List_Courses($account_id, $name, $code, $group, $lecturer, $semester, $year, $bd)
// Returns the list of courses
{
	if (!empty($account_id))
		$condition .= " AND (accounts.account_id='$account_id')";
	if (!empty($name))
		$condition .= " AND (accounts.name = '$name')";
	if (!empty($code))
		$condition .= " AND (courses.code = '$code')";
	if (!empty($group))
		$condition .= " AND (courses.class = '$group')";
	if (!empty($lecturer))
		$condition .= " AND (courses.lecturer = '$lecturer')";
	if (!empty($semester))
		$condition .= " AND (courses.semester = $semester)";
	if (!empty($year))
		$condition .= " AND (courses.year = $year)";

	$query = "SELECT * FROM accounts, courses WHERE (accounts.account_id = courses.account_id)$condition";

	$result = $bd->Query($query);

	$courses = "";
	$num_courses = $bd->NumberOfRows($result);

	if ($num_courses)
	{
		for ($i = 0; $i < $num_courses; $i++)
		{
			$courses[$i][0] = $bd->FetchResult($result, $i, 'account_id');
			$courses[$i][1] = $bd->FetchResult($result, $i, 'name');
			$courses[$i][2] = $bd->FetchResult($result, $i, 'commentaries');
			$courses[$i][3] = $bd->FetchResult($result, $i, 'code');
			$courses[$i][4] = $bd->FetchResult($result, $i, 'class');
			$courses[$i][5] = $bd->FetchResult($result, $i, 'semester');
			$courses[$i][6] = $bd->FetchResult($result, $i, 'year');
			$courses[$i][7] = $bd->FetchResult($result, $i, 'lecturer');
			$person = List_People($courses[$i][7], '', '', '', '', $bd);
			$courses[$i][8] = $person[0][1];
		}
	}
	return $courses;
}

function Insert_Course($account_id, $name, $comments, $code, $group, $year, $semester, $lecturer, $bd)
// Inserts a new course
{
	$query = "INSERT INTO accounts VALUES ('$account_id', 'course', '$name', '$comments')";
	$bd->Query($query);
	$query = "INSERT INTO courses VALUES ('$account_id', '$code', '$group', $year, $semester, '$lecturer')";
	$bd->Query($query);
}

function Delete_Course($account_id, $bd)
// Deletes a given course, and all its dependencies
{
	$query = "DELETE FROM accounts WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	$query = "DELETE FROM courses WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	$query = "DELETE FROM appointments WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	$query = "DELETE FROM weekly_appointments WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	/*
	Include commands to delete group related information!!!!!!!
	*/

	return 1;
}

function Update_Course($account_id, $name, $comments, $code, $group, $year, $semester, $lecturer, $bd)
// Updates a course
{
	$query = "UPDATE accounts SET name='$name', commentaries = '$comments' WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	$query1 = "UPDATE courses SET class='$group', code='$code', year=$year, semester=$semester, lecturer='$lecturer' WHERE account_id = '$account_id'";
	$result = $bd->Query($query1);
}

Function List_Permissions($perm_id, $master_id, $group_id, $category_id, $slave_id, $role, $bd)
// Returns the list of permissions
{
	if (!empty($perm_id))
		$condition .= " AND (rel_id='$perm_id')";
	if (!empty($master_id))
		$condition .= " AND (master_id = '$master_id')";
	if (!empty($group_id))
		$condition .= " AND (master_group = $group_id)";
	if (!empty($category_id))
		$condition .= " AND (master_category = '$category_id')";
	if (!empty($slave_id))
		$condition .= " AND (slave_id = '$slave_id')";
	if (!empty($role))
		$condition .= " AND (rel_type = '$role')";

	$query = "SELECT * FROM relationships WHERE (rel_id IS NOT NULL)$condition";
	$result = $bd->Query($query);

	$permissions = "";
	$num_permissions = $bd->NumberOfRows($result);

	if ($num_permissions)
	{
		for ($i = 0; $i < $num_permissions; $i++)
		{
			$permissions[$i][0] = $bd->FetchResult($result, $i, 'rel_id');
			$permissions[$i][1] = $bd->FetchResult($result, $i, 'master_id');
			if ($permissions[$i][1])
			{
				$person = List_People($permissions[$i][1], '', '', '', '', $bd);
				$permissions[$i][2] = $person[0][1];
			}
			else
				$permissions[$i][2] = '';
			$permissions[$i][3] = $bd->FetchResult($result, $i, 'master_group');
			if ($permissions[$i][3] > 0)
			{
				$group = $permissions[$i][3];
				$query1 = "SELECT name FROM groups WHERE group_id = $group";
				$result1 = $bd->Query($query1);
				$permissions[$i][4] = $bd->FetchResult($result1, 0, 'name');
			}
			else
			{
				$permissions[$i][3] = '';
				$permissions[$i][4] = '';
			}
			$permissions[$i][5] = $bd->FetchResult($result, $i, 'master_category');
			if ($permissions[$i][5])
			{
				$cat = List_Categories($permissions[$i][5], '', $bd);
				$result1 = $bd->Query($query1);
				$permissions[$i][6] = $cat[0][1];
			}
			else
				$permissions[$i][6] = '';
			$permissions[$i][7] = $bd->FetchResult($result, $i, 'slave_id');
			$permissions[$i][9] = $bd->FetchResult($result, $i, 'rel_type');
			$role = $permissions[$i][9];
			if ($role == 'course')
			{
				$course = List_Courses($permissions[$i][7], '', '', '', '', '', '', $bd);
				$permissions[$i][8] = $course[0][1];
			}
			else{
				$room = List_Rooms($permissions[$i][7], '', '', '', '',  $bd);
				$permissions[$i][8] = $room[0][1];
			}
		}
	}
	return $permissions;
}

function List_Rooms_Permission($account_id, $bd)
// Returns a list of Rooms a user is allowed to manipulate
{
	$rooms = array();

	// Includes the permissions related to the user
	$result_user = $bd->Query("SELECT * FROM relationships WHERE master_id = '$account_id' AND
		rel_type = 'room'");
	for ($i = 0; $i < $bd->NumberOfRows($result_user); $i++)
	{
		$room_id = $bd->FetchResult($result_user, $i, 'slave_id');
		$room = List_Rooms($room_id, '', '', '', '',  $bd);
		$rooms[] = $room[0];
	}

	// Include the permissions related to the groups which the user is member of
	$query_groups = "SELECT * FROM group_members WHERE account_id = '$account_id'";
	$result_groups = $bd->Query($query_groups);
	for ($i = 0; $i < $bd->NumberOfRows($result_groups); $i++)
	{
		$group_id = $bd->FetchResult($result_groups, $i, 'group_id');
		$query_perm_group = "SELECT * FROM relationships WHERE master_group = '$group_id' AND
			rel_type = 'room'";
		$result_group = $bd->Query($query_perm_group);
		for ($j = 0; $j < $bd->NumberOfRows($result_group); $j++)
		{
			$room_id = $bd->FetchResult($result_group, $j, 'slave_id');
			$room = List_Rooms($room_id, '', '', '', '',  $bd);
			$rooms[] = $room[0];
		}
	}

	// Include the permissions related to the user's category
	$query_cat = "SELECT * FROM people WHERE account_id = '$account_id'";
	$result_cat = $bd->Query($query_cat);
	$cat_id = $bd->FetchResult($result_cat, 0, 'category');

	$query_perm_cat = "SELECT * FROM relationships WHERE master_category = '$cat_id' AND
			rel_type = 'room'";
	$result_category = $bd->Query($query_perm_cat);
	for ($j = 0; $j < $bd->NumberOfRows($result_category); $j++)
	{
		$room_id = $bd->FetchResult($result_category, $j, 'slave_id');
		$room = List_Rooms($room_id, '', '', '', '',  $bd);
		$rooms[] = $room[0];
	}

	return $rooms;
}

function List_Courses_Permission($account_id, $bd)
// Returns a list of Courses a user is allowed to manipulate
{
	$courses = array();

	// Includes the permissions related to the user
	$result_user = $bd->Query("SELECT * FROM relationships WHERE master_id = '$account_id' AND
		rel_type = 'course'");
	for ($i = 0; $i < $bd->NumberOfRows($result_user); $i++)
	{
		$course_id = $bd->FetchResult($result_user, $i, 'slave_id');
		$course = List_Courses($course_id, '', '', '', '', '', '', $bd);
		$courses[] = $course[0];
	}

	// Include the permissions related to the groups which the user is member of
	$query_groups = "SELECT * FROM group_members WHERE account_id = '$account_id'";
	$result_groups = $bd->Query($query_groups);
	for ($i = 0; $i < $bd->NumberOfRows($result_groups); $i++)
	{
		$group_id = $bd->FetchResult($result_groups, $i, 'group_id');
		$query_perm_group = "SELECT * FROM relationships WHERE master_group = '$group_id' AND
			rel_type = 'course'";
		$result_group = $bd->Query($query_perm_group);
		for ($j = 0; $j < $bd->NumberOfRows($result_group); $j++)
		{
			$course_id = $bd->FetchResult($result_group, $j, 'slave_id');
			$course = List_Courses($course_id, '', '', '', '', '', '', $bd);
			$course[] = $course[0];
		}
	}

	// Include the permissions related to the user's category
	$query_cat = "SELECT * FROM people WHERE account_id = '$account_id'";
	$result_cat = $bd->Query($query_cat);
	$cat_id = $bd->FetchResult($result_cat, 0, 'category');

	$query_perm_cat = "SELECT * FROM relationships WHERE master_category = '$cat_id' AND
			rel_type = 'course'";
	$result_category = $bd->Query($query_perm_cat);
	for ($j = 0; $j < $bd->NumberOfRows($result_category); $j++)
	{
		$course_id = $bd->FetchResult($result_category, $j, 'slave_id');
		$course = List_Courses($course_id, '', '', '', '', '', '', $bd);
		$course[] = $course[0];
	}

	return $courses;
}

function Delete_Permission($perm_id, $bd)
// Deletes a permission
{
	$query = "DELETE FROM relationships WHERE rel_id = $perm_id";
	$result = $bd->Query($query);
	return 1;
}

Function Insert_Permission($master_id, $group_id, $category_id, $slave_id, $role, $bd)
// Inserts a new permission
{
	if (empty($group_id))
		$group_id = "NULL";
	$query = "INSERT INTO relationships VALUES (NULL, '$master_id', $group_id, '$category_id',
		'$slave_id', '$role')";
	$bd->Query($query);
}

?>
