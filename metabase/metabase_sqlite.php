<?php
if(!defined("METABASE_SQLITE_INCLUDED"))
{
	define("METABASE_SQLITE_INCLUDED",1);

/*
 *	metabase_sqlite.php
 *
 *	@(#) $Header: /cvsroot/phpsecurityadm/metabase/metabase_sqlite.php,v 1.1.1.1 2003/02/27 20:55:35 koivi Exp $
 *	@author	Jeroen Derks <jeroen@derks.it>
 *
 */
 
class metabase_sqlite_class extends metabase_database_class
{
	var $connection=0;
	var $connected_database_file;
	var $create_mask=0644;
	var $decimal_factor=1.0;
	var $results=array();
	var $highest_fetched_row=array();
	var $columns=array();
	var $escape_quotes="'";
	var $sequence_prefix="_sequence_";
	var $manager_class_name="metabase_manager_sqlite_class";
	var $manager_include="manager_sqlite.php";
	var $manager_included_constant="METABASE_MANAGER_SQLITE_INCLUDED";
	var $base_transaction_name="___php_metabase_sqlite_auto_commit_off";
	var $version='';
	var $encoding='';

	Function GetDatabaseFile($database_name)
	{
		$database_path=(IsSet($this->options["DatabasePath"]) ? $this->options["DatabasePath"] : "");
		$database_extension=(IsSet($this->options["DatabaseExtension"]) ? $this->options["DatabaseExtension"] : ".db");
		return($database_path.$database_name.$database_extension);
	}

	Function Connect()
	{
		if(!function_exists('sqlite_version'))
			return($this->SetError("Connect","SQLite support is not available in this PHP configuration"));

		$version=sqlite_version();
		if($version)
			$this->version=explode(".",$version);
		$this->encoding=sqlite_encoding();

		$database_file=$this->GetDatabaseFile($this->database_name);
		if($this->connection!=0)
		{
			if (!strcmp($this->connected_database_file,$database_file))
				return(1);

			sqlite_close($this->connection);
			$this->connection=0;
			$this->affected_rows=-1;
		}
		if(!strcmp($this->database_name,""))
			return(1);

		if(!@file_exists($database_file))
			return($this->SetError("Connect","database does not exist"));
		if(($this->connection=@sqlite_open($database_file,$this->create_mask))==0)
			return($this->SetError("Connect",IsSet($php_errormsg) ? $php_errormsg : "Could not open SQLite database"));
		if(IsSet($this->supported["Transactions"])
		&& !$this->auto_commit)
		{
			$this->Debug("Query: BEGIN TRANSACTION $this->base_transaction_name");
			if(!@sqlite_exec("BEGIN TRANSACTION $this->base_transaction_name;",$this->connection))
			{
				sqlite_close($this->connection);
				$this->connection=0;
				$this->affected_rows=-1;
				return($this->SetError("Connect",IsSet($php_errormsg) ? $php_errormsg : "Could not start transaction"));
			}
			$this->RegisterTransactionShutdown(0);
		}
		$this->connected_database_file=$database_file;
		return(1);
	}

	Function Close()
	{
		if($this->connection!=0)
		{
			if(IsSet($this->supported["Transactions"])
			&& !$this->auto_commit)
				$this->AutoCommitTransactions(1);
			sqlite_close($this->connection);
			$this->connection=0;
			$this->affected_rows=-1;
		}
	}

	Function Query($query)
	{
		$this->Debug("Query: $query");
		$first=$this->first_selected_row;
		$limit=$this->selected_row_limit;
		$this->first_selected_row=$this->selected_row_limit=0;
		if(!strcmp($this->database_name,""))
			return($this->SetError("Query","it was not specified a valid database name to select"));
		if(!$this->Connect())
			return(0);
		if(GetType($space=strpos($query_string=strtolower(ltrim($query))," "))=="integer")
			$query_string=substr($query_string,0,$space);
		if(($select=($query_string=="select" || $query_string=="show" || $query_string=="explain" ))
		&& $limit>0)
			$query.=" LIMIT $limit OFFSET $first";
		if(($result=@sqlite_exec($query.';',$this->connection)))
		{
			$this->results[$result]=array();
			if($select)
				$this->highest_fetched_row[$result]=-1;
			else
				$this->affected_rows=sqlite_changes($this->connection);
			UnSet($this->columns[$result]);
		}
		else
			return($this->SetError("Query",$php_errormsg));
		return($result);
	}

	Function Replace($table,&$fields)
	{
		$count=count($fields);
		for($keys=0,$query=$values="",Reset($fields),$field=0;$field<$count;Next($fields),$field++)
		{
			$name=Key($fields);
			if($field>0)
			{
				$query.=",";
				$values.=",";
			}
			$query.=$name;
			if(IsSet($fields[$name]["Null"])
			&& $fields[$name]["Null"])
				$value="NULL";
			else
			{
				if(!IsSet($fields[$name]["Value"]))
					return($this->SetError("Replace","it was not specified a value for the $name field"));
				switch(IsSet($fields[$name]["Type"]) ? $fields[$name]["Type"] : "text")
				{
					case "text":
						$value=$this->GetTextFieldValue($fields[$name]["Value"]);
						break;
					case "boolean":
						$value=$this->GetBooleanFieldValue($fields[$name]["Value"]);
						break;
					case "integer":
						$value=strval($fields[$name]["Value"]);
						break;
					case "decimal":
						$value=$this->GetDecimalFieldValue($fields[$name]["Value"]);
						break;
					case "float":
						$value=$this->GetFloatFieldValue($fields[$name]["Value"]);
						break;
					case "date":
						$value=$this->GetDateFieldValue($fields[$name]["Value"]);
						break;
					case "time":
						$value=$this->GetTimeFieldValue($fields[$name]["Value"]);
						break;
					case "timestamp":
						$value=$this->GetTimestampFieldValue($fields[$name]["Value"]);
						break;
					default:
						return($this->SetError("Replace","it was not specified a supported type for the $name field"));
				}
			}
			$values.=$value;
			if(IsSet($fields[$name]["Key"])
			&& $fields[$name]["Key"])
			{
				if($value=="NULL")
					return($this->SetError("Replace","key values may not be NULL"));
				$keys++;
			}
		}
		if($keys==0)
			return($this->SetError("Replace","it were not specified which fields are keys"));
		return($this->Query("REPLACE INTO $table ($query) VALUES($values)"));
	}

	Function EndOfResult($result)
	{
		if(!IsSet($this->highest_fetched_row[$result]))
		{
			$this->SetError("End of result","attempted to check the end of an unknown result");
			return(-1);
		}
		return($this->highest_fetched_row[$result]>=$this->NumberOfRows($result)-1);
	}

	Function Fetch($result)
	{
		if(!$this->results[$result])
			if(!($this->results[$result]=@sqlite_fetch_array($result)))
				return($this->SetError("Fetch result array",$php_errormsg));
		return(1);
	}

	Function FetchResult($result,$row,$field)
	{
		if(!$this->Fetch($result))
			return(0);
		if(is_string($field))
		{
			if(!$this->columns[$result])
				$this->GetColumnNames($result,$dummy);
			$field=$this->columns[$result][$field];
		}
		$this->highest_fetched_row[$result]=max($this->highest_fetched_row[$result],$row);
		return($this->results[$result][$row][$field]);
	}

	Function FetchResultArray($result,&$array,$row)
	{
		if(!$this->Fetch($result))
			return(0);
		$array=$this->results[$result][$row];
		$this->highest_fetched_row[$result]=max($this->highest_fetched_row[$result],$row);
		return($this->ConvertResultRow($result,$array));
	}

	Function FetchCLOBResult($result,$row,$field)
	{
		return($this->FetchLOBResult($result,$row,$field));
	}

	Function FetchBLOBResult($result,$row,$field)
	{
		return($this->FetchLOBResult($result,$row,$field));
	}

	Function ConvertResult(&$value,$type)
	{
		switch($type)
		{
			case METABASE_TYPE_BOOLEAN:
				$value=(strcmp($value,"Y") ? 0 : 1);
				return(1);
			case METABASE_TYPE_DECIMAL:
				$value=sprintf("%.".$this->decimal_places."f",doubleval($value)/$this->decimal_factor);
				return(1);
			case METABASE_TYPE_FLOAT:
				$value=doubleval($value);
				return(1);
			case METABASE_TYPE_DATE:
			case METABASE_TYPE_TIME:
			case METABASE_TYPE_TIMESTAMP:
				return(1);
			default:
				return($this->BaseConvertResult($value,$type));
		}
	}

	Function NumberOfRows($result)
	{
		if(!$this->Fetch($result))
			return(0);
		return(count($this->results[$result]));
	}

	Function FreeResult(&$result)
	{
		UnSet($this->highest_fetched_row[$result]);
		UnSet($this->columns[$result]);
		UnSet($this->result_types[$result]);
		UnSet($this->results[$result]);
		return(1);
	}

	Function GetCLOBFieldTypeDeclaration($name,&$field)
	{
		if(IsSet($field["length"]))
		{
			$length=$field["length"];
			if($length<=255)
				$type="TINYTEXT";
			else
			{
				if($length<=65535)
					$type="TEXT";
				else
				{
					if($length<=16777215)
						$type="MEDIUMTEXT";
					else
						$type="LONGTEXT";
				}
			}
		}
		else
			$type="LONGTEXT";
		return("$name $type".(IsSet($field["notnull"]) ? " NOT NULL" : ""));
	}

	Function GetBLOBFieldTypeDeclaration($name,&$field)
	{
		if(IsSet($field["length"]))
		{
			$length=$field["length"];
			if($length<=255)
				$type="TINYBLOB";
			else
			{
				if($length<=65535)
					$type="BLOB";
				else
				{
					if($length<=16777215)
						$type="MEDIUMBLOB";
					else
						$type="LONGBLOB";
				}
			}
		}
		else
			$type="LONGBLOB";
		return("$name $type".(IsSet($field["notnull"]) ? " NOT NULL" : ""));
	}

	Function GetIntegerFieldTypeDeclaration($name,&$field)
	{
		return("$name ".(IsSet($field["unsigned"]) ? "INT UNSIGNED" : "INT").(IsSet($field["default"]) ? " DEFAULT ".$field["default"] : "").(IsSet($field["notnull"]) ? " NOT NULL" : ""));
	}

	Function GetDateFieldTypeDeclaration($name,&$field)
	{
		return($name." DATE".(IsSet($field["default"]) ? " DEFAULT '".$field["default"]."'" : "").(IsSet($field["notnull"]) ? " NOT NULL" : ""));
	}

	Function GetTimestampFieldTypeDeclaration($name,&$field)
	{
		return($name." DATETIME".(IsSet($field["default"]) ? " DEFAULT '".$field["default"]."'" : "").(IsSet($field["notnull"]) ? " NOT NULL" : ""));
	}

	Function GetTimeFieldTypeDeclaration($name,&$field)
	{
		return($name." TIME".(IsSet($field["default"]) ? " DEFAULT '".$field["default"]."'" : "").(IsSet($field["notnull"]) ? " NOT NULL" : ""));
	}

	Function GetFloatFieldTypeDeclaration($name,&$field)
	{
		if(IsSet($this->options["FixedFloat"]))
			$this->fixed_float=$this->options["FixedFloat"];
		else
		{
			if($this->connection==0)
				$this->Connect();
		}
		return("$name DOUBLE".($this->fixed_float ? "(".($this->fixed_float+2).",".$this->fixed_float.")" : "").(IsSet($field["default"]) ? " DEFAULT ".$this->GetFloatFieldValue($field["default"]) : "").(IsSet($field["notnull"]) ? " NOT NULL" : ""));
	}

	Function GetDecimalFieldTypeDeclaration($name,&$field)
	{
		return("$name BIGINT".(IsSet($field["default"]) ? " DEFAULT ".$this->GetDecimalFieldValue($field["default"]) : "").(IsSet($field["notnull"]) ? " NOT NULL" : ""));
	}

	Function EscapeText(&$text)
	{
		if(strcmp($this->escape_quotes,"'"))
			$text=str_replace($this->escape_quotes,$this->escape_quotes.$this->escape_quotes,$text);
		$text=str_replace("'",$this->escape_quotes."'",$text);
	}

	Function GetCLOBFieldValue($prepared_query,$parameter,$clob,&$value)
	{
		for($value="'";!MetabaseEndOfLOB($clob);)
		{
			if(MetabaseReadLOB($clob,$data,$this->lob_buffer_length)<0)
			{
				$value="";
				return($this->SetError("Get CLOB field value",MetabaseLOBError($clob)));
			}
			$this->EscapeText($data);
			$value.=$data;
		}
		$value.="'";
		return(1);			
	}

	Function FreeCLOBValue($prepared_query,$clob,&$value,$success)
	{
		Unset($value);
	}

	Function GetBLOBFieldValue($prepared_query,$parameter,$blob,&$value)
	{
		for($value="'";!MetabaseEndOfLOB($blob);)
		{
			if(!MetabaseReadLOB($blob,$data,$this->lob_buffer_length))
			{
				$value="";
				return($this->SetError("Get BLOB field value",MetabaseLOBError($clob)));
			}
			$this->EscapeText($data);
			$value.=$data;
		}
		$value.="'";
		return(1);			
	}

	Function FreeBLOBValue($prepared_query,$blob,&$value,$success)
	{
		Unset($value);
	}

	Function GetFloatFieldValue($value)
	{
		return(!strcmp($value,"NULL") ? "NULL" : "$value");
	}

	Function GetDecimalFieldValue($value)
	{
		return(!strcmp($value,"NULL") ? "NULL" : strval(round(doubleval($value)*$this->decimal_factor)));
	}

	Function GetColumnNames($result,&$column_names)
	{
		$result_value=intval($result);
		if(!IsSet($this->highest_fetched_row[$result_value]))
			return($this->SetError("Get column names","it was specified an inexisting result set"));
		if(!IsSet($this->columns[$result_value]))
		{
			$this->columns[$result_value]=array();
			$fields=sqlite_fetch_field_array($result);
			$columns=count($fields);
			for($column=0;$column<$columns;$column++)
				$this->columns[$result_value][strtolower($fields[$column])]=$column;
		}
		$column_names=$this->columns[$result_value];
		return(1);
	}

	Function NumberOfColumns($result)
	{
		if(!IsSet($this->highest_fetched_row[intval($result)]))
		{
			$this->SetError("Get number of columns","it was specified an inexisting result set");
			return(-1);
		}
		$this->GetColumnNames($result,$dummy);
		return(count($dummy));
	}

	Function GetSequenceNextValue($name,&$value)
	{
		$sequence_name=$this->sequence_prefix.$name;
		if(!($result=$this->Query("INSERT INTO $sequence_name (sequence) VALUES (NULL)")))
			return(0);
		$value=intval(sqlite_last_insert_rowid($this->connection));
		if(!$this->Query("UPDATE $sequence_name SET sequence=$value WHERE ROWID=$value"))
			return(0);
		if(!$this->Query("DELETE FROM $sequence_name WHERE sequence<$value"))
			$this->warning="could delete previous sequence table values";
		return(1);
	}

	Function AutoCommitTransactions($auto_commit)
	{
		$this->Debug("AutoCommit: ".($auto_commit ? "On" : "Off"));
		if(!IsSet($this->supported["Transactions"]))
			return($this->SetError("Auto-commit transactions","transactions are not in use"));
		if(((!$this->auto_commit)==(!$auto_commit)))
			return(1);
		if($this->connection)
		{
			if($auto_commit)
			{
				if(!$this->Query("END TRANSACTION $this->base_transaction_name"))
					return(0);
			}
			else
			{
				if(!$this->Query("BEGIN TRANSACTION $this->base_transaction_name"))
					return(0);
			}
		}
		$this->auto_commit=$auto_commit;
		return($this->RegisterTransactionShutdown($auto_commit));
	}

	Function CommitTransaction()
	{
 		$this->Debug("Commit Transaction");
		if(!IsSet($this->supported["Transactions"]))
			return($this->SetError("Commit transaction","transactions are not in use"));
		if($this->auto_commit)
			return($this->SetError("Commit transaction","transaction changes are being auto commited"));
		if(!$this->Query("COMMIT TRANSACTION $this->base_transaction_name"))
			return(0);
		return($this->Query("BEGIN TRANSACTION $this->base_transaction_name"));
	}

	Function RollbackTransaction()
	{
 		$this->Debug("Rollback Transaction");
		if(!IsSet($this->supported["Transactions"]))
			return($this->SetError("Rollback transaction","transactions are not in use"));
		if($this->auto_commit)
			return($this->SetError("Rollback transaction","transactions can not be rolled back when changes are auto commited"));
		if(!$this->Query("ROLLBACK TRANSACTION $this->base_transaction_name"))
			return(0);
		return($this->Query("BEGIN TRANSACTION $this->base_transaction_name"));
	}

	Function Setup()
	{
		$this->supported["Sequences"]=
		$this->supported["Indexes"]=
		$this->supported["AffectedRows"]=
		$this->supported["SummaryFunctions"]=
		$this->supported["OrderByText"]=
		$this->supported["GetSequenceCurrentValue"]=
		$this->supported["SelectRowRanges"]=
		$this->supported["Replace"]=
		$this->supported["Transactions"]=
			1;
		$this->decimal_factor=pow(10.0,$this->decimal_places);
		return("");
	}
};

}
?>
