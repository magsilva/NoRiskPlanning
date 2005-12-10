<?
/* This is the No Risk Planning Application Program Interface
 * Here are implemented the main functions that are used to
 * manipulate users, schedules and other features of the System.
 * To use it, it's required that the component Metabase is
 * installed and running.
*/

function User_Authenticate_Password($account_id, $password, $bd)
/* The password must be informed in its natural way
 * Checks whether the password and account_id were informed correctly
 * Returns 1 if OK, and 0 if the password is wrong, and -1 if user was not found
*/
{
	$query = "SELECT * FROM accounts WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	if ($result)
	{
		if ($bd->NumberOfRows($result))
		{
			$query_password = "SELECT * FROM people WHERE account_id = '$account_id'";
			$result1 = $bd->Query($query_password);
			if ($bd->NumberOfRows($result1)){
				$actual_password = $bd->FetchResult($result1, 0, 'password');
				$crypt_password = md5($password);

				if ($crypt_password == $actual_password)
					return 1;
				else
					return 0;
			}
			else
				return 0;
		}
		else
			return -1;
	}
	else
		return -1;
}

function Person_Update_Profile($account_id, $dep_id, $name, $role, $email, $url, $category,
	$commentaries, $password, $image_enable, $bd)
/* Updates a user profile
 * The password must be informed in its natural way. If the password
 * is not supposed to be change, it must be informed as 0
 * Returns 1 if the update was done, and 0 if there was any error
*/
{
	$query = "SELECT * FROM people WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	if ($result)
	{
		if ($bd->NumberOfRows($result))
		{
			$current_password = $bd->FetchResult($result, 0, 'password');
			$crypt_new_password = md5("$password");

			if (empty($password))
				$passwd_update = $current_password;
			else
				$passwd_update = $crypt_new_password;
				$update = "UPDATE accounts SET name='$name', role='$role', commentaries='$commentaries'
					WHERE account_id = '$account_id'";
				$result = $bd->Query($update);
				$update1 = "UPDATE people SET dep_id = $dep_id, email = '$email', url='$url',
					category='$category', password = '$passwd_update', public_types = '$image_enable' 					WHERE account_id = '$account_id'";
				$result1 = $bd->Query($update1);
			return 1;
		}
		else
			return 0;
	}
	else
		return 0;
}

function User_List_Groups($user, $bd)
/* Returns an array with all the information about the groups
 * a user is member
*/
{
	$query = "SELECT * FROM group_members where account_id = '$user'";
	$result = $bd->Query($query);
	for ($i = 0; $i < $bd->NumberOfRows($result); $i++)
	{
		$group_id = $bd->FetchResult($result, $i, 'group_id');
		$query1 = "SELECT * FROM groups WHERE group_id = '$group_id'";
		$result1 = $bd->Query($query1);
		$groups[$i][0] = $group_id;
		$groups[$i][1] = $bd->FetchResult($result1, 0, 'category');
		$groups[$i][2] = $bd->FetchResult($result1, 0, 'name');
		$groups[$i][3] = $bd->FetchResult($result1, 0, 'acronym');
		$groups[$i][4] = $bd->FetchResult($result1, 0, 'description');
		$groups[$i][6] = $bd->FetchResult($result, $i, 'membership');
	}
	return ($groups);
}


function User_List_Rooms($user, $bd)
/* Returns an array with all the information about the rooms a
 * user is able to handle
*/
{
	$query = "SELECT * FROM people WHERE account_id = '$user'";
	$result = $bd->Query($query);
	$category = $bd->FetchResult($result, 0, 'category');
	if ($bd->NumberOfRows($result))
	{
		$groups = User_List_Groups($user, $bd);

		$condition = "";

		for ($i = 0; $groups[$i]; $i++)
		{
			$condition .= " OR (master_group =". $groups[$i][0].")";
		}

		$query = "SELECT * FROM relationships WHERE ((master_id = '$user') OR (master_category = '$category') 
			$condition) AND (rel_type = 'room')";
			
		$result = $bd->Query($query);
		$condition2 = "";
		for ($i = 0; $i < $bd->NumberOfRows($result); $i++)
		{
			$rooms_aux[$i] = $bd->FetchResult($result, $i, 'slave_id');
			if ($i == 0)
				$condition = "(account_id = '$rooms_aux[$i]')";
			else
				$condition .= " OR (account_id = '$rooms_aux[$i]')";
		}
		
		if ($bd->NumberOfRows($result))
		{
			$query = "SELECT * FROM accounts WHERE $condition";
			$result = $bd->Query($query);
			$query1 = "SELECT * FROM rooms WHERE $condition";
			$result1 = $bd->Query($query1);
			for ($i = 0; $i < $bd->NumberOfRows($result); $i++)
			{
				$rooms[$i][0] = $bd->FetchResult($result, $i, 'account_id');
				$rooms[$i][1] = $bd->FetchResult($result1, $i, 'code');
				$rooms[$i][2] = $bd->FetchResult($result, $i, 'name');
				$rooms[$i][3] = $bd->FetchResult($result1, $i, 'capacity');
				$rooms[$i][4] = $bd->FetchResult($result1, $i, 'location');
				$rooms[$i][5] = $bd->FetchResult($result, $i, 'commentaries');
			}
			return $rooms;
		}
	}
}

function User_List_Courses($user, $bd)
/* Returns an array with all the information about the courses a
 * user is able to handle
*/
{
	$query = "SELECT * FROM people WHERE account_id = '$user'";
	$result = $bd->Query($query);
	$category = $bd->FetchResult($result, 0, 'category');
	if ($bd->NumberOfRows($result))
	{
		$groups = User_List_Groups($user, $bd);

		$condition = "";

		for ($i = 0; $groups[$i]; $i++)
		{
			$condition .= " OR (master_group =". $groups[$i][0].")";
		}

		$query = "SELECT * FROM relationships WHERE ((master_id = '$user') OR (master_category = '$category') 
			$condition) AND (rel_type = 'course')";
			
		$result = $bd->Query($query);
		$condition2 = "";
		for ($i = 0; $i < $bd->NumberOfRows($result); $i++)
		{
			$courses_aux[$i] = $bd->FetchResult($result, $i, 'slave_id');
			if ($i == 0)
				$condition = "(account_id = '$courses_aux[$i]')";
			else
				$condition .= " OR (account_id = '$courses_aux[$i]')";
		}
		
		if ($bd->NumberOfRows($result))
		{
			$query = "SELECT * FROM accounts WHERE $condition ORDER BY account_id";
			$result = $bd->Query($query);
			$query1 = "SELECT * FROM courses WHERE $condition ORDER BY account_id";
			$result1 = $bd->Query($query1);
			for ($i = 0; $i < $bd->NumberOfRows($result); $i++)
			{
				$courses[$i][0] = $bd->FetchResult($result, $i, 'account_id');
				$courses[$i][1] = $bd->FetchResult($result, $i, 'name');
				$courses[$i][2] = $bd->FetchResult($result, $i, 'commentaries');
				$courses[$i][3] = $bd->FetchResult($result1, $i, 'code');
				$courses[$i][4] = $bd->FetchResult($result1, $i, 'class');
				$courses[$i][5] = $bd->FetchResult($result1, $i, 'semester');
				$courses[$i][6] = $bd->FetchResult($result1, $i, 'year');
				$courses[$i][7] = $bd->FetchResult($result1, $i, 'lecturer');
			}
			return $courses;
		}
	}
}

function Get_Role($account_id, $bd)
{
	$query = "SELECT * FROM accounts WHERE account_id = '$account_id'";
	$result = $bd->Query($query);
	if ($bd->NumberOfRows($result) > 0)
		return ($bd->FetchResult($result, 0, 'role'));
	else
		return ('');
}

require_once "api_validation.php";
require_once "api_appointments.php";
require_once "api_admin.php";
require_once "api_groups.php";
?>