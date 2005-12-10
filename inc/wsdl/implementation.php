<?php
/* 
 * Declare the functions which are called when the above methods are used 
 */
function GetAppointments($account_id, $beg_date, $end_date)
{
	global $bd;
	global $cfg;

        $query = "SELECT * FROM accounts WHERE account_id = '$account_id'";
        $result = $bd->Query($query);
        $role = $bd->FetchResult($result, 0, 'role');
        $var_type = $role . '_type';
        $var_color = $role . '_color';
        $var_image = $role . '_icon';
        $array_type = $cfg[$var_type];
        $array_color = $cfg[$var_color];
        $array_image = $cfg[$var_image];

        $apps = Retrieve_Appointments($account_id, $beg_date, $end_date, '', '', '',
                $cfg['time'], $array_type, $array_color, $array_image, $cfg['days'], $fake, $bd);

	return ($apps);
}

function GetAllGroups()
{
	global $bd;

    $groups = List_Groups('', '', '', '', 0, $bd);

    $groups_aux = array();
    for ($i = 0; $i < count($groups); $i++)
    {
        $group = $groups[$i];
        $groups_aux[$i]["group.id"] = $group[0];
        $groups_aux[$i]["group.name"] = $group[1];
        $groups_aux[$i]["group.category"] = $group[2];
        $groups_aux[$i]["group.acronym"] = $group[3];
        $groups_aux[$i]["group.description"] = $group[4];
        $members = $groups[$i][5];
        for ($j = 0; $j < count($members); $j++)
        {
            $groups_aux[$i]["group.members"][$j]["group.member.id"]=$members[$j][0];
            $groups_aux[$i]["group.members"][$j]["group.member.name"]=$members[$j][1];
            $groups_aux[$i]["group.members"][$j]["group.member.membership"]=$members[$j][2];
        }
    }

    $result = array('schedule' => $groups_aux);

	return ($result);
}
?>
