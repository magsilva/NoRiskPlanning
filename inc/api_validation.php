<?php

function User_Validate_Numeric_Field($content)
/* Checks whether the field was filled in properly
 * If it returns 0, the field was empty
 * If it returns -1, if is not numeric
 * If it returns -2, a SQL injection may be happening
*/
{
	if (empty($content))
		return 0;
	else{
		if (strstr($content, ";"))
			return -2;
		else{
			if ($content > 0 )
				return 1;
			else
				return -1;
 		}
	}
}

function User_Validate_Simple_Field($content, $length)
/* Checks whether the field was filled in properly
 * If it returns 0, the field was empty
 * If it returns -1, the length was larger than the limit
 * If it returns -2, a SQL injection may be happening
*/
{
	if (empty($content))
		return 0;
	else{
		if (strstr($content, ";"))
			return -2;
		else{
			if (strlen($content) > $length)
				return -1;
			else
				return 1;
 		}
	}
}

function User_Validate_Password($content, $min_length)
/* Checks whether the field was filled in properly
 * If it returns 0, the field was empty
 * If it returns -1, the length was smaller than the limit
*/
{
	if (empty($content))
		return 0;
	else{
		if (strlen($content) < $milength)
			return -1;
		else
			return 1;
	}
}


function User_Validate_Email($email, $length)
/* Checks whether an email address was filled in correctly
 * If it returns 0, the email doesn't correpond to the format string@string
 * If it returns -1, the length was larger than the limit
*/
{
	if (!ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.'@'.'[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.
		'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $email))
	// If the email contains only valid characteres, followed by @
		return 0;
	else{
		if (strlen($email) > $length)
			return(-1);
		else
			return 1;
	}
}

function User_Validate_New_Email($account_id, $email, $bd)
/* Checks whether a new email address may be used to a user
 * and whether it's properly filled in
*/
{
	if (User_Validate_Email($email))
	{
		$query = "SELECT * FROM accounts wHERE (email = '$email') AND (account_id <> '$account_id')
			AND (role = 'User')";
		$result = $bd->Query($query);
		if ($result)
		{
			if ($bd->NumberOfRows($result))
				return 0;
			else
				return 1;
		}
		else
			return 0;
	}
	else
		return 0;
}

?>
