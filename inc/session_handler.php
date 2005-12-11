<?
/* This module is reponsible for the functions related
 * to No Risk Planning session handling
 */
function Create_Session($username, $exibition, $ip_address, $master_session, $bd)
/* Creates a session on the database
 * Requires the type of exibition and the user's IP Address
 * If the session is a master session, this value must be 0
 */
{
	$query = "SELECT * FROM sessions WHERE (account_id = " . $bd->GetTextFieldValue($username) . " AND (active = 1)";
	$active = $bd->Query($query);

	// Check if there is an unfinished session
	if ($num_sessions = $bd->NumberOfRows($active) != 0)
	{
		if ($master_session == 0)
		{
			for ($i = 0; $i < $num_sessions; $i++)
			{
				$sess_id = $bd->FetchResult($active, $i, 'session_id');
				Terminate_Session($sess_id, 'forced_termination', $bd);
			}
		}
	}
	
	$insert = "INSERT INTO sessions VALUES(NULL, $master_session, '$username', now(), now(),
		1, '', '$ip_address', '$exibition')";
	$bd->Query($insert);
	$res_sess_id = $bd->Query("SELECT * FROM sessions WHERE active=1 AND account_id='$username' ORDER
		BY session_id DESC");
	$sess_id = $bd->FetchResult($res_sess_id, 0, 'session_id');
	return ($sess_id);
}

function Terminate_Session($sess_id, $end_cause, $bd)
/* Terminates a user session
 * Requires the session id and the end cause
 */
{
	$query = "SELECT * FROM sessions WHERE session_id = $sess_id";
	$result = $bd->Query($query);
	if ($result)
	{
		if ($bd->NumberOfRows($result))
		{
			$bd->Query("UPDATE sessions SET active=0, end_cause='$end_cause', end=now() WHERE session_id = $sess_id");
			return 1;
		}
		else
		{
			return 0;
		}
	}
	else
	{
		return 0;
	}
}

function Get_Account_Id($sess_id, &$account_id, $bd)
/* Gets the account_id of a given session */
{
	$query = "SELECT * FROM sessions WHERE session_id = $sess_id";
	$result = $bd->Query($query);
	if ($result)
	{
		if ($bd->NumberOfRows($result))
		{
			$account_id = $bd->FetchResult($result, 0, 'account_id');
			return 1;
		}
		else
		{
			return 0;
		}
	}
	else
	{
		return 0;
	}
}

function Get_Exibition($sess_id, $bd)
/* Gets the exibition mode of a given session */
{
	$query = "SELECT * FROM sessions WHERE session_id = $sess_id";
	$result = $bd->Query($query);
	if ($result)
	{
		if ($bd->NumberOfRows($result))
		{
			$exibition = $bd->FetchResult($result, 0, 'exibition');
			return $exibition;
		}
		else
		{
			return 0;
		}
	}
	else
	{
		return 0;
	}
}

function Get_Master_Session($sess_id, $bd)
/* Gets the id of the master session of a given_session_id */
{
	$query = "SELECT * FROM sessions WHERE session_id = $sess_id";
	$result = $bd->Query($query);
	if ($result)
	{
		if ($bd->NumberOfRows($result))
		{
			$master_session_id = $bd->FetchResult($result, 0, 'master_session_id');
			return $master_session_id;
		}
		else
		{
			return 0;
		}
	}
	else
	{
		return 0;
	}
}

function Get_Crypt_Master_Session($sess_id, $bd)
/* Gets the encrypted master session id */
{
	$master = Get_Master_Session($sess_id, $bd);
	if (!($master == 0))
	{
		$master = md5($sess_id) . $sess_id;
	}
	return $master;
}

function Validate_Session($complete_sess_id, $ip, $bd)
/* Returns if a session id and its respective
 * md5 code is valid of not
 */
{
	$complete_sess_id = "$complete_sess_id";
	$sess_id = substr($complete_sess_id, 32);
	$crypt_sess_id = md5($sess_id);
	$informed_crypt_sess_id = substr($complete_sess_id, 0, 32);
	if ($informed_crypt_sess_id == $crypt_sess_id)
	// If the informed encrypted code is equal to the actual encrypted code
	{
		$query = "SELECT * FROM sessions where session_id = $sess_id";
		$result = $bd->Query($query);
		if ($result)
		{
			if ($bd->NumberOfRows($result))
			// If the session exists
			{
				$active = $bd->FetchResult($result, 0, 'active');
				$session_ip = $bd->FetchResult($result, 0, 'ip_address');
				if ($active && ($session_ip == $ip) )
				{
					$update = "UPDATE sessions SET end=now() WHERE session_id = $sess_id";
					$bd->Query($update);
					return 1;
				}
				else
					return 0;
			}
			else
			{
				return 0;
			}
		}
	}
	else
	{
		return 0;
	}
}
?>
