<?
if(!defined("METABASE_MANAGER_ODBC_INCLUDED"))
{
	define("METABASE_MANAGER_ODBC_INCLUDED",1);

/*
 * manager_odbc.php
 *
 * @(#) $Header: /cvsroot/phpsecurityadm/metabase/manager_odbc.php,v 1.1.1.1 2003/02/27 20:55:09 koivi Exp $
 *
 */

class metabase_manager_odbc_class extends metabase_manager_database_class
{
	Function CreateDatabase(&$db,$name)
	{
		$current_dba_access=$db->dba_access;
		$db->dba_access=1;
		$success=$db->Query("CREATE DATABASE $name");
		$db->dba_access=$current_dba_access;
		return($success);
	}

	Function DropDatabase(&$db,$name)
	{
		$current_dba_access=$db->dba_access;
		$db->dba_access=1;
		$success=$db->Query("DROP DATABASE $name");
		$db->dba_access=$current_dba_access;
		return($success);
	}
};
?>
