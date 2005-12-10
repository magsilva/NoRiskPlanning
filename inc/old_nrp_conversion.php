<?php
/* This script is used to convert the database from version 1.3 to version 2.0
 * It only will work if the time setting is default, and if the user types 
 * are default.
*/

require("script_inicialization.php");

$old_bd_name = "nrp";         // Name of the old database
$old_bd_host = "localhost";   // Name of the host where the old database is
$old_bd_user = "user";        // Mysql user name of the old database
$old_bd_pswd = "password";    // Password for the mysql connection to the old database

function convert_day($day)
{
	switch($day)
	{
		case "Sunday": return 0; break;
		case "Monday": return 1; break;
		case "Tuesday": return 2; break;
		case "Wednesday": return 3; break;
		case "Thursday": return 4; break;
		case "Friday": return 5; break;
		case "Saturday": return 6; break;
	}
}

function convert_time($time)
{
	switch($day)
	{
		case "07:00": return 0; break;
		case "08:00": return 1; break;
		case "09:00": return 2; break;
		case "10:00": return 3; break;
		case "11:00": return 4; break;
		case "12:00": return 5; break;
		case "13:00": return 6; break;
		case "14:00": return 7; break;
		case "15:00": return 8; break;
		case "16:00": return 9; break;
		case "17:00": return 10; break;
		case "18:00": return 11; break;
		case "19:00": return 12; break;
		case "20:00": return 13; break;
		case "21:00": return 14; break;
		case "22:00": return 15; break;
		case "23:00": return 16; break;
	}
}

if (!$conn) echo "Error connecting server";

$db = mysql_select_db("$old_bd_name", $conn);

if (!$db) echo "Error connecting database";

$queryunit = "SELECT * FROM proj_unidades";

$resultunit = mysql_query($queryunit);

for ($i = 0; $unit = mysql_fetch_array($resultunit); $i++)
{
	$unit_id = $unit['unidade_id'];
	$unit_name = $unit['nome'];
	$unit_acronym = $unit['sigla'];
	$unit_desc = $unit['descricao'];
	$insert = "INSERT INTO units VALUES ($unit_id, '$unit_name', '$unit_acronym', '$unit_desc')"
	$bd->Query($insert);
}

for ($i = 0; $dep = mysql_fetch_array($resultunit); $i++)
{
	$dep_id = $dep['departamento_id'];
	$dep_unitid = $dep['unidade_id'];
	$dep_name = $dep['nome'];
	$dep_acronym = $dep['sigla'];
	$dep_desc = $dep['descricao'];
	$insert = "INSERT INTO departments VALUES ($dep_id, $dep_unitid, '$dep_name', '$dep_acronym', '$dep_desc')"
	$bd->Query($insert);
}

$query1 = "SELECT * FROM proj_contas";

$result1 = mysql_query($query1);

for ($i = 0; $account = mysql_fetch_array($result1); $i++)
{
	$acc_id = $account['conta_id'];
	$acc_dep = $account['departamento_id'];
	$acc_name = $account['nome'];
	$acc_surname = $account['ult_nome'];
	$acc_email = $account['email'];
	$acc_url = $account['pagina'];
	$acc_password = $account['senha'];
	$acc_comments = $account['comentarios'];
	$insert = "INSERT INTO accounts VALUES ('$acc_id', 'user', '$acc_name $acc_surname', '$acc_comments')"
	$bd->Query($insert);
	$im_enable = "";
	for ($j = 0; $j < count($cfg['user_type']); $j++)
		$im_enable .= "0";
	$insert = "INSERT INTO people VALUES ('$acc_id', '$acc_email', $acc_dep, '$acc_url', '$acc_password', '$im_enable', '0')";
	$bd->Query($insert);

	$query_weekly = "SELECT * FROM proj_compromissos WHERE conta_id = '$acc_id'";
	$result_weekly = mysql_query($query_weekly);
	for ($j = 0; $weekly = mysql_fetch_array($result_weekly); $j++)
	{
		$app_id = $weekly['compr_id'];
		$app_accountid = $weekly['account_id'];
		$app_desc = $weekly['descricao'];
		$app_type = $weekly['tipo'];
		$app_beg = $weekly['horario_id_inicio'];
		$app_end = $weekly['horario_id_fim'];
		$app_url = $weekly['url'];
		$query_beg = "SELECT * FROM proj_horarios WHERE horario_id = $app_beg";
		$result_beg = mysql_query($query_beg);
		$time_beg = mysql_fetch_array($query_beg);
		$query_end = "SELECT * FROM proj_horarios WHERE horario_id = $app_end";
		$result_end = mysql_query($query_end);
		$time_end = mysql_fetch_array($query_end);
		$day_beg = explode("-", $time_beg['nome']);
		$day_beg1 = explode(" ", $day_beg[0]);
		$day_beg2 = explode(" ", $day_beg[1]);
		$time_beg = convert_time($day_beg2[1]);
		$day_beg = convert_day($day_beg1[0]);
		$day_end = explode("-", $time_end['nome']);
		$day_end2 = explode(" ", $day_end[1]);
		$time_end = convert_time($day_beg2[1]);
		$insert = "INSERT INTO weekly_appointments VALUES (NULL, '$app_accountid', '$app_desc', 0, $day_beg, 
			$time_beg, $time_end, '$app_url', '$app_accountid', 0, 0, 1, '')";
		$bd->Query($insert);
	}

	$query_app = "SELECT * FROM proj_eventuais WHERE conta_id = '$acc_id'";
	$result_app = mysql_query($query_app);
	for ($j = 0; $app = mysql_fetch_array($result_app); $j++)
	{
		$app_id = $app['compr_id'];
		$app_accountid = $app['account_id'];
		$app_desc = $weekly['descricao'];
		$app_type = $weekly['tipo'];
		$app_beg = $weekly['horario_id_inicio'];
		$app_end = $weekly['horario_id_fim'];
		$app_url = $weekly['url'];
		$app_date = $weekly['data_inicio'];
		$query_beg = "SELECT * FROM proj_horarios WHERE horario_id = $app_beg";
		$result_beg = mysql_query($query_beg);
		$time_beg = mysql_fetch_array($query_beg);
		$query_end = "SELECT * FROM proj_horarios WHERE horario_id = $app_end";
		$result_end = mysql_query($query_end);
		$time_end = mysql_fetch_array($query_end);
		$day_beg = explode("-", $time_beg['nome']);
		$day_beg2 = explode(" ", $day_beg[1]);
		$time_beg = convert_time($day_beg2[1]);
		$day_end = explode("-", $time_end['nome']);
		$day_end2 = explode(" ", $day_end[1]);
		$time_end = convert_time($day_beg2[1]);
		$insert = "INSERT INTO appointments VALUES (NULL, '$app_accountid', '$app_desc', 0, '$app_date', 
			$time_beg, $time_end, '$app_url', '$app_accountid', 0, 0, 1, '')";
		$bd->Query($insert);
	}
}

$groups = "SELECT * FROM proj_grupos";
$query_groups = mysql_query($groups);

for ($i = 0; $group = mysql_fetch_array($query_groups); $i++)
{
	$group_id = $group['id_grupo'];
	$group_name = $group['name'];
	$group_acronym = $group['sigla'];
	$group_desc = $group['descricao'];
	$group_categ = $group['categoria_id'];
	$query_categ = "SELECT * FROM proj_categ_grupos WHERE categ_id = $group_categ";
	$result_categ = mysql_query($query_categ);
	$categ = mysql_fetch_array($result_categ);
	$group_categ = $categ['nome'];

	$insert = "INSERT INTO groups VALUES ($group_id, '$group_categ', '$group_name', '$group_acronym', '$group_desc')";
	$bd->Query($insert);

	$query_members = "SELECT * FROM proj_grupos_membros WHERE group_id = $group_id";
	$result_members = mysql_query($query_members);
	for ($j = 0; $member = mysql_fetch_array($result_members); $j++)
	{
		$account_id = $member['conta_id'];
		$membership = $member['cargo'];
		if ($membership == 'Criador')
			$member_membership = 'O';
		else
			$member_membership = 'C';
		$insert = "INSERT INTO group_members VALUES ('$group_id', '$account_id', '$member_membership', '')";
		$result = $bd->Query($insert);
	}
}

?>
