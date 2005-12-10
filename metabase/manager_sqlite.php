<?php
if(!defined("METABASE_MANAGER_SQLITE_INCLUDED"))
{
	define("METABASE_MANAGER_SQLITE_INCLUDED",1);

/*
 *	manager_sqlite.php
 *
 *	@(#) $Header: /cvsroot/phpsecurityadm/metabase/manager_sqlite.php,v 1.1.1.1 2003/02/27 20:55:26 koivi Exp $
 *	@author	Jeroen Derks <jeroen@derks.it>
 *
 */

class metabase_manager_sqlite_class extends metabase_manager_database_class
{
	Function CreateDatabase(&$db,$name)
	{
		$database_file=$db->GetDatabaseFile($name);
		if (@file_exists($database_file))
			return($db->SetError("Create database","database already exists"));
		$success=@touch($database_file);
		if(!$success)
			return($db->SetError("Create database",$php_errormsg));
		return(1);
	}

	Function DropDatabase(&$db,$name)
	{
		$database_file=$db->GetDatabaseFile($name);
		if (!@file_exists($database_file))
			return($db->SetError("Drop database","database does not exist"));
		$success=@unlink($database_file);
		if(!$success)
			return($db->SetError("Drop database",$php_errormsg));
		return(1);
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

	Function ListTables(&$db,&$tables)
	{
		if(!$db->QueryColumn("SELECT name FROM sqlite_master WHERE type='table' AND sql NOT NULL ORDER BY name",$table_names))
			return(0);
		$prefix_length=strlen($db->sequence_prefix);
		for($tables=array(),$table=0;$table<count($table_names);$table++)
		{
			if(substr($table_names[$table],0,$prefix_length)!=$db->sequence_prefix)
				$tables[]=$table_names[$table];
		}
		return(1);
	}

	Function ListTableFields(&$db,$table,&$fields)
	{
		if(!($result=$db->Query("SELECT sql FROM sqlite_master WHERE type='table' AND name='$table'")))
			return(0);
		if(!$db->GetColumnNames($result,$columns))
		{
			$db->FreeResult($result);
			return(0);
		}
		if(!IsSet($columns["sql"]))
		{
			$db->FreeResult($result);
			return($db->SetError("List table fields","show columns does not return the table creation sql"));
		}

		$sql=$db->FetchResult($result,0,0);
		$this->GetTableColumnNames($sql,$fields);

		$db->FreeResult($result);
		return(1);
	}

	Function GetTableColumnNames($sql,&$column_names)
	{
		$this->GetTableColumns($sql,$columns);
		$count=count($columns);
		if($count==0)
			return;
		$column_names=array();
		for($i=0;$i<$count;++$i)
			$column_names[]=$columns[$i]["name"];
	}

	Function GetTableColumns($sql,&$columns)
	{
		$start_pos=strpos($sql,"(");
		$end_pos=strrpos($sql,")");
		$column_def=substr($sql,$start_pos+1,$end_pos-$start_pos-1);
		$column_sql=split(",",$column_def);
		$columns=array();
		$count=count($column_sql);
		if($count==0)
			return;
		for($i=0,$j=0;$i<$count;++$i)
		{
			$k=$i;
			if (strstr($column_sql[$i],"("))
			{
				while(!strstr($column_sql[$k],")"))
				{
					$column_sql[$i].=",".$column_sql[++$k];
					UnSet($column_sql[$k]);
				}
			}
			$lower=strtolower($column_sql[$i]);
			$columns[$j]["name"]=strtok($lower," ");
			$db_type=strtok("(), ");
			if($db_type=="national")
				$db_type=strtok("(), ");
			if($db_type)
				$columns[$j]["type"]=$db_type;
			$length=strtok("(");
			if(!is_numeric($length))
				UnSet($length);
			else
				$columns[$j]["length"]=$db_type;
			$decimal=strtok(",) ");
			if($decimal)
				$columns[$j]["decimal"]=$decimal;
			if(($not_null_str=strstr($lower," not null ")))
				$columns[$j]["notnull"]=1;
			if(($not_null_str=strstr($lower," primary ")))
				$columns[$j]["primary"]=1;
			if(($not_null_str=strstr($lower," unique ")))
				$columns[$j]["unique"]=1;
			UnSet($default);
			if(($default_pos=strpos($lower," default ")))
			{
				$default_str=trim(substr($column_sql[$i],$default_pos+9));
				$first=$default_str[0];
				if($first=="\""||$first=="\'")
					$default=strtok(substr($default_str,1),$first);
				else
					$default=strtok($default_str," ");
				$columns[$j]["default"]=$default;
			}
			++$j;
			$i=$k;
		}
	}

	Function GetTableFieldDefinition(&$db,$table,$field,&$definition)
	{
		$field_name=strtolower($field);
		if($field_name==$db->dummy_primary_key)
			return($db->SetError("Get table field definition",$db->dummy_primary_key." is an hidden column"));
		if(!($result=$db->Query("SELECT sql FROM sqlite_master WHERE type='table' AND name='$table'")))
			return(0);
		if(!$db->GetColumnNames($result,$columns))
		{
			$db->FreeResult($result);
			return(0);
		}
		if(!IsSet($columns["sql"]))
		{
			$db->FreeResult($result);
			return($db->SetError("Get table field definition","show columns does not return the table creation sql"));
		}

		$sql=$db->FetchResult($result,0,0);
		$this->GetTableColumns($sql,$columns);
		$count=count($columns);
		for($i=0;$i<$count;++$i)
		{
			if($field_name==$columns[$i]["name"])
			{
				$db_type=$columns[$i]["type"];
				$length=$columns[$i]["length"];
				$decimal=$columns[$i]["decimal"];
				$notnull=$columns[$i]["notnull"];
				$default=$columns[$i]["default"];
				
				$type=array();
				switch($db_type)
				{
					case "tinyint":
					case "smallint":
					case "mediumint":
					case "int":
					case "integer":
					case "bigint":
						$type[0]="integer";
						if($length=="1")
							$type[1]="boolean";
						break;

					case "tinytext":
					case "mediumtext":
					case "longtext":
					case "text":
					case "char":
					case "varchar":
						$type[0]="text";
						if($decimal=="binary")
							$type[1]="blob";
						elseif($length=="1")
							$type[1]="boolean";
						elseif(strstr($db_type,"text"))
							$type[1]="clob";
						break;

					case "enum":
					case "set":
						$type[0]="text";
						$type[1]="integer";
						break;

					case "date":
						$type[0]="date";
						break;

					case "datetime":
					case "timestamp":
						$type[0]="timestamp";
						break;

					case "time":
						$type[0]="time";
						break;

					case "float":
					case "double":
					case "real":
						$type[0]="float";
						break;

					case "decimal":
					case "numeric":
						$type[0]="decimal";
						break;

					case "tinyblob":
					case "mediumblob":
					case "longblob":
					case "blob":
						$type[0]="blob";
						break;

					case "year":
						$type[0]="integer";
						$type[1]="date";
						break;

					default:
						return($db->SetError("Get table field definition","unknown database attribute type"));
				}

				for($definition=array(),$datatype=0;$datatype<count($type);$datatype++)
				{
					$definition[$datatype]=array(
						"type"=>$type[$datatype]
					);
					if(IsSet($notnull))
						$definition[$datatype]["notnull"]=1;
					if(IsSet($default))
						$definition[$datatype]["default"]=$default;
					if(strlen($length))
						$definition[$datatype]["length"]=$length;
				}
				$db->FreeResult($result);
				return(1);
			}
		}
		$db->FreeResult($result);
		return($db->SetError("Get table field definition","it was not specified an existing table column"));
	}

	Function ListTableIndexes(&$db,$table,&$indexes)
	{
		if(!($result=$db->Query("SELECT name FROM sqlite_master WHERE type='index' AND tbl_name='$table' AND sql NOT NULL ORDER BY name")))
			return(0);
		if($db->NumberOfRows($result)==0)
		{
			$db->FreeResult($result);
			return(1);
		}
		if(!$db->GetColumnNames($result,$columns))
		{
			$db->FreeResult($result);
			return(0);
		}
		if(!IsSet($columns["name"]))
		{
			$db->FreeResult($result);
			return($db->SetError("List table indexes","show index does not return the index name"));
		}

		$name_column=$columns["name"];
		for($found=$indexes=array(),$index=0;!$db->EndOfResult($result);$index++)
		{
			$index_name=$db->FetchResult($result,$index,$name_column);
			if(!IsSet($found[$index_name]))
			{
				$indexes[]=$index_name;
				$found[$index_name]=1;
			}
		}

		$db->FreeResult($result);
		return(1);
	}

	Function GetTableIndexDefinition(&$db,$table,$index,&$definition)
	{
		$index_name=strtolower($index);
		if($index_name=="PRIMARY")
			return($db->SetError("Get table index definition","PRIMARY is an hidden index"));
		if(!($result=$db->Query("SELECT sql FROM sqlite_master WHERE type='index' AND name='$index' AND tbl_name='$table' AND sql NOT NULL ORDER BY name")))
			return(0);
		if(!$db->GetColumnNames($result,$columns))
		{
			$db->FreeResult($result);
			return(0);
		}
		if(!IsSet($columns[$column="sql"]))
		{
			$db->FreeResult($result);
			return($db->SetError("Get table index definition","show index does not return the table creation sql"));
		}

		$sql=strtolower($db->FetchResult($result,0,0));
		$unique=strstr($sql," unique ");
		$key_name=$index;
		$start_pos=strpos($sql,"(");
		$end_pos=strrpos($sql,")");
		$column_names=substr($sql,$start_pos+1,$end_pos-$start_pos-1);
		$column_names=split(",",$column_names);

		$definition=array();
		if($unique)
			$definition["unique"]=1;
		$count=count($column_names);
		for($i=0;$i<$count;++$i)
		{
			$column_name=strtok($column_names[$i]," ");
			$collation=strtok(" ");
			$definition["FIELDS"][$column_name]=array();
			if(!empty($collation))
				$definition["FIELDS"][$column_name]["sorting"]=($collation=="ASC" ? "ascending" : "descending");
		}

		$db->FreeResult($result);
		return(IsSet($definition["FIELDS"]) ? 1 : $db->SetError("Get table index definition","it was not specified an existing table index"));
	}

	Function ListSequences(&$db,&$sequences)
	{
		if(!$db->QueryColumn("SELECT name FROM sqlite_master WHERE type='table' AND sql NOT NULL ORDER BY name",$table_names))
			return(0);
		$prefix_length=strlen($db->sequence_prefix);
		for($sequences=array(),$table=0;$table<count($table_names);$table++)
		{
			if(substr($table_names[$table],0,$prefix_length)==$db->sequence_prefix)
				$sequences[]=substr($table_names[$table],$prefix_length);
		}
		return(1);
	}

	Function GetSequenceDefinition(&$db,$sequence,&$definition)
	{
		if(!$db->QueryColumn("SELECT name FROM sqlite_master WHERE type='table' AND name='$db->sequence_prefix$sequence' AND sql NOT NULL ORDER BY name",$table_names))
			return(0);
		$prefix_length=strlen($db->sequence_prefix);
		for($table=0;$table<count($table_names);$table++)
		{
			if(substr($table_names[$table],0,$prefix_length)==$db->sequence_prefix
			&& !strcmp(substr($table_names[$table],$prefix_length),$sequence))
			{
				if(!$db->QueryField("SELECT MAX(sequence) FROM ".$table_names[$table],$start))
					return(0);
				$definition=array("start"=>$start+1);
				return(1);
			}
		}
		return($db->SetError("Get sequence definition","it was not specified an existing sequence"));
	}

	Function CreateSequence(&$db,$name,$start)
	{
		if(!$db->Query("CREATE TABLE _sequence_$name (sequence INTEGER PRIMARY KEY DEFAULT 0 NOT NULL)"))
			return(0);
		if($db->Query("INSERT INTO _sequence_$name (sequence) VALUES (".($start-1).")"))
			return(1);
		$error=$db->Error();
		if(!$db->Query("DROP TABLE _sequence_$name"))
			$db->warning="could not drop inconsistent sequence table";
		return($db->SetError("Create sequence",$error));
	}

	Function DropSequence(&$db,$name)
	{
		return($db->Query("DROP TABLE _sequence_$name"));
	}

	Function GetSequenceCurrentValue(&$db,$name,&$value)
	{
		if(!($result=$db->Query("SELECT MAX(sequence) FROM ".$db->sequence_prefix.$name)))
			return(0);
		if(!($db->FetchResultField($result,$value)))
			$value=0;
		return(1);
	}

	Function CreateIndex(&$db,$table,$name,&$definition)
	{
		$query="CREATE ".(IsSet($definition["unique"]) ? "UNIQUE" : "")." INDEX $name ON $table (";
		for($field=0,Reset($definition["FIELDS"]);$field<count($definition["FIELDS"]);$field++,Next($definition["FIELDS"]))
		{
			if($field>0)
				$query.=",";
			$query.=Key($definition["FIELDS"]);
		}
		$query.=")";
		return($db->Query($query));
	}

	Function DropIndex(&$db,$table,$name)
	{
		return($db->Query("DROP INDEX $name"));
	}
};
}
?>
