<?
if(!defined("METABASE_MANAGER_DATABASE_INCLUDED"))
{
	define("METABASE_MANAGER_DATABASE_INCLUDED",1);

/*
 * manager_database.php
 *
 * @(#) $Header: /cvsroot/nrp/nrp/metabase/manager_database.php,v 1.1.1.1 2004/05/20 19:37:21 apfreire Exp $
 *
 */

class metabase_manager_database_class
{
	/* PRIVATE METHODS */

	Function GetField(&$db,&$field,$field_name,&$query)
	{
		if(!strcmp($field_name,""))
			return($db->SetError("Get field","it was not specified a valid field name (\"$field_name\")"));
		switch($field["type"])
		{
			case "integer":
				$query=$db->GetIntegerFieldTypeDeclaration($field_name,$field);
				break;
			case "text":
				$query=$db->GetTextFieldTypeDeclaration($field_name,$field);
				break;
			case "clob":
				$query=$db->GetCLOBFieldTypeDeclaration($field_name,$field);
				break;
			case "blob":
				$query=$db->GetBLOBFieldTypeDeclaration($field_name,$field);
				break;
			case "boolean":
				$query=$db->GetBooleanFieldTypeDeclaration($field_name,$field);
				break;
			case "date":
				$query=$db->GetDateFieldTypeDeclaration($field_name,$field);
				break;
			case "timestamp":
				$query=$db->GetTimestampFieldTypeDeclaration($field_name,$field);
				break;
			case "time":
				$query=$db->GetTimeFieldTypeDeclaration($field_name,$field);
				break;
			case "float":
				$query=$db->GetFloatFieldTypeDeclaration($field_name,$field);
				break;
			case "decimal":
				$query=$db->GetDecimalFieldTypeDeclaration($field_name,$field);
				break;
			default:
				return($db->SetError("Get field","type \"".$field["type"]."\" is not yet supported"));
		}
		return(1);
	}

	Function GetFieldList(&$db,&$fields,&$query_fields)
	{
		for($query_fields="",Reset($fields),$field_number=0;$field_number<count($fields);$field_number++,Next($fields))
		{
			if($field_number>0)
				$query_fields.=",";
			$field_name=Key($fields);
			if(!$this->GetField($db,$fields[$field_name],$field_name,$query))
				return(0);
			$query_fields.=$query;
		}
		return(1);
	}

	/* PUBLIC METHODS */

	Function CreateDatabase(&$db,$database)
	{
		return($db->SetError("Create database","database creation is not supported"));
	}

	Function DropDatabase(&$db,$database)
	{
		return($db->SetError("Drop database","database dropping is not supported"));
	}

	Function CreateTable(&$db,$name,&$fields)
	{
		if(!IsSet($name)
		|| !strcmp($name,""))
			return($db->SetError("Create table","it was not specified a valid table name"));
		if(count($fields)==0)
			return($db->SetError("Create table","it were not specified any fields for table \"$name\""));
		$query_fields="";
		if(!$this->GetFieldList($db,$fields,$query_fields))
			return(0);
		return($db->Query("CREATE TABLE $name ($query_fields)"));
	}

	Function DropTable(&$db,$name)
	{
		return($db->Query("DROP TABLE $name"));
	}

	Function AlterTable(&$db,$name,&$changes,$check)
	{
		return($db->SetError("Alter table","database table alterations are not supported"));
	}

	Function ListTables(&$db,&$tables)
	{
		return($db->SetError("List tables","list tables is not supported"));
	}

	Function ListTableFields(&$db,$table,&$fields)
	{
		return($db->SetError("List table fields","list table fields is not supported"));
	}

	Function GetTableFieldDefinition(&$db,$table,$field,&$definition)
	{
		return($db->SetError("Get table field definition","get table field definition is not supported"));
	}

	Function ListTableIndexes(&$db,$table,&$indexes)
	{
		return($db->SetError("List table indexes","list table indexes is not supported"));
	}

	Function GetTableIndexDefinition(&$db,$table,$index,&$definition)
	{
		return($db->SetError("Get table index definition","get table index definition is not supported"));
	}

	Function ListSequences(&$db,&$sequences)
	{
		return($db->SetError("List sequences","list sequences is not supported"));
	}

	Function GetSequenceDefinition(&$db,$sequence,&$definition)
	{
		return($db->SetError("Get sequence definition","get sequence definition is not supported"));
	}

	Function CreateIndex(&$db,$table,$name,&$definition)
	{
		$query="CREATE";
		if(IsSet($definition["unique"]))
			$query.=" UNIQUE";
		$query.=" INDEX $name ON $table (";
		for($field=0,Reset($definition["FIELDS"]);$field<count($definition["FIELDS"]);$field++,Next($definition["FIELDS"]))
		{
			if($field>0)
				$query.=",";
			$field_name=Key($definition["FIELDS"]);
			$query.=$field_name;
			if($db->Support("IndexSorting")
			&& IsSet($definition["FIELDS"][$field_name]["sorting"]))
			{
				switch($definition["FIELDS"][$field_name]["sorting"])
				{
					case "ascending":
						$query.=" ASC";
						break;
					case "descending":
						$query.=" DESC";
						break;
				}
			}
		}
		$query.=")";
		return($db->Query($query));
	}

	Function DropIndex(&$db,$table,$name)
	{
		return($db->Query("DROP INDEX $name"));
	}

	Function CreateSequence(&$db,$name,$start)
	{
		return($db->SetError("Create sequence","sequence creation is not supported"));
	}

	Function DropSequence(&$db,$name)
	{
		return($db->SetError("Drop sequence","sequence dropping is not supported"));
	}

	Function GetSequenceCurrentValue(&$db,$name,&$value)
	{
		return($db->SetError("Get sequence current value","getting sequence current value is not supported"));
	}
};
}
?>