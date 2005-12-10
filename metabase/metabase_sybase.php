<?
if(!defined("METABASE_SYBASE_INCLUDED"))
{
 define("METABASE_SYBASE_INCLUDED",1);

/*
 * metabase_sybase.php
 *
 * @(#) $Header: /cvsroot/phpsecurityadm/metabase/metabase_sybase.php,v 1.1.1.1 2003/02/27 20:55:35 koivi Exp $
 *
 */
class metabase_sybase_class extends metabase_database_class
{
 var $connection=0;
 var $connected_host;
 var $connected_user;
 var $connected_password;
 var $opened_persistent="";
 var $selected_database;
 
 var $current_row=array();
 var $fetched_row=array();
 var $columns=array();
 var $highest_fetched_row=array();
 var $ranges=array();

 Function SetError($error)
 {
  if(($this->last_error=sybase_get_last_message())=="")
   $this->last_error=$error;
 }

 Function DoQuery($query)
 {
  $result=@sybase_query($query,$this->connection);
  $result_value=intval($result);
  $this->current_row[$result_value]=$this->affected_rows=-1;
  if($result) {
   // get the identity for an insert
   if (eregi("^insert", $query)) {
    if ($resid=@sybase_query("SELECT @@IDENTITY",$this->connection)) {
     if (is_array($rowid=sybase_fetch_row($resid)))
      $value=intval($rowid[0]);
     else
      $value=0;
     sybase_free_result($resid);
     $this->last_error=$value;
    }
   }
  }
  return($result);
 }

 Function Close()
 {
  if($this->connection!=0)
  {
   if(!$this->auto_commit)
    $this->DoQuery("ROLLBACK TRANSACTION");
   sybase_close($this->connection);
   $this->connection=0;
   $this->affected_rows=-1;
  }
 }

 Function StandaloneQuery($query)
 {
  if(!function_exists("sybase_connect"))
  {
   $this->last_error="Sybase support is not available in this PHP configuration";
   return(0);
  }
  if(($connection=sybase_connect($this->host,$this->user,$this->password))==0)
  {
   $this->SetError("Could not connect to the Sybase server");
   return(0);
  }
  if(!($success=@sybase_query($query,$connection)))
   $this->SetError("Could not query a Sybase server");
  sybase_close($connection);
  return($success);
 }

 Function CreateDatabase($name)
 {
  return($this->StandaloneQuery("CREATE DATABASE $name ON ".(IsSet($this->options["DatabaseDevice"]) ? $this->options["DatabaseDevice"] : "DEFAULT").(IsSet($this->options["DatabaseSize"]) ? "=".$this->options["DatabaseSize"] : "")));
 }

 Function DropDatabase($name)
 {
  return($this->StandaloneQuery("DROP DATABASE $name"));
 }

 Function AlterTable($name,$changes,$check)
 {
  if($check)
  {
   for($change=0,Reset($changes);$change<count($changes);Next($changes),$change++)
   {
    switch(Key($changes))
    {
     case "AddedFields":
      break;
     case "RemovedFields":
     case "name":
     case "RenamedFields":
     case "ChangedFields":
      $this->last_error="change type \"".Key($changes)."\" is not supported by the server";
      return(0);
     default:
      $this->last_error="change type \"".Key($changes)."\" not yet supported";
      return(0);
    }
   }
   return(1);
  }
  else
  {
   if(IsSet($changes[$change="RemovedFields"])
   || IsSet($changes[$change="name"])
   || IsSet($changes[$change="RenamedFields"])
   || IsSet($changes[$change="ChangedFields"]))
   {
    $this->last_error="change type \"$change\" is not supported by the server";
    return(0);
   }
   $query="";
   if(IsSet($changes["AddedFields"]))
   {
    if(strcmp($query,""))
     $query.=", ";
    $query.="ADD ";
    $fields=$changes["AddedFields"];
    for($field=0,Reset($fields);$field<count($fields);Next($fields),$field++)
    {
     if(strcmp($query,""))
      $query.=", ";
     $query.=$fields[Key($fields)]["Declaration"];
    }
   }
   return(strcmp($query,"") ? $this->Query("ALTER TABLE $name $query") : 1);
  }
 }

 Function Connect()
 {
  if($this->connection!=0)
  {
   if(!strcmp($this->connected_host,$this->host)
   && !strcmp($this->connected_user,$this->user)
   && !strcmp($this->connected_password,$this->password)
   && $this->opened_persistent==$this->persistent)
    return(1);
   $this->Close();
  }
  $function=($this->persistent ? "sybase_pconnect" : "sybase_connect");
  if(!function_exists($function))
  {
   $this->last_error="Sybase support is not available in this PHP configuration";
   return(0);
  }
  if(($this->connection=@$function($this->host,$this->user,$this->password))<=0)
  {
   $this->SetError("Could not connect to the Sybase server");
   return(0);
  }
  if(!$this->auto_commit
  && !$this->DoQuery("BEGIN TRANSACTION"))
  {
   sybase_close($this->connection);
   $this->connection=0;
   return(0);
  }
  $this->connected_host=$this->host;
  $this->connected_user=$this->user;
  $this->connected_password=$this->password;
  $this->opened_persistent=$this->persistent;
  return(1);
 }

  Function SelectDatabase()
 {
  if(!strcmp($this->database_name,""))
  {
   $this->last_error="It was not specified a valid database name to select";
   return(0);
  }
  $last_connection=$this->connection;
  if(!$this->Connect())
   return(0);
  if($last_connection==$this->connection
  && strcmp($this->selected_database,"")
  && !strcmp($this->selected_database,$this->database_name))
   return(1);
  if(!sybase_select_db($this->database_name,$this->connection))
  {
   $this->SetError("Could not select a Sybase database");
   return(0);
  }
  $this->selected_database=$this->database_name;
  return(1);
 }

  Function PrepareQuery($query)
 {
  $positions=array();

  $counted=preg_match_all("/([=\(,]\s*\?\s*[,\)wW])/",$query,$regs);
  $position=-1;
  for($i=0;$i<$counted;$i++)
  {
   $position=strpos($query,$regs[0][$i],$position+1)+strpos($regs[0][$i],"?",0);
   $positions[]=$position;
  }
  $this->prepared_queries[]=array(
   "Query"=>$query,
   "Positions"=>$positions,
   "Values"=>array()
  );
  $prepared_query=count($this->prepared_queries);
  if($this->selected_row_limit>0)
  {
   $this->prepared_queries[$prepared_query]["First"]=$this->first_selected_row;
   $this->prepared_queries[$prepared_query]["Limit"]=$this->selected_row_limit;
  }
  return($prepared_query);
 }

Function Query($query)
 {
  $first=$this->first_selected_row;
  $limit=$this->selected_row_limit;
  $this->first_selected_row=$this->selected_row_limit=0;
  if(!$this->SelectDatabase())
   return(0);
  if((($select=(strtolower(strtok($query," \n\r"))=="begin"))
  || ($select=(strtolower(strtok($query," "))=="select"))
  || ($select=(strtolower(strtok($query," "))=="fetch")))
  && $limit>0 && $first>0)
  {
   $result=0;
   if($this->DoQuery("DECLARE select_cursor CURSOR FOR $query FOR READ ONLY")
   && $this->DoQuery("OPEN select_cursor")
   && ($result=$this->DoQuery("FETCH select_cursor")))
   {
    if(sybase_num_rows($result)==1)
    {
     if ($first > 0)
     {
	  $dummy=sybase_fetch_row($result);
	  $i=0;
	 }
	 else
	 {
      $this->ranges[$result][0]=sybase_fetch_row($result);
	  $i=1;
	 }
     for($row=1;$row<$first+$limit;$row++)
     {
      if(!($row_result=$this->DoQuery("FETCH select_cursor")))
      {
       Unset($this->ranges[$result]);
       sybase_free_result($result);
       $result=0;
       break;
      }
      if(sybase_num_rows($row_result)==0)
	  {
	   if (!IsSet($this->ranges[$result]))
	   {
        sybase_free_result($result);
	    $result=$row_result;
	   }
       break;
	  }
	  if ($row < $first)
       $dummy=sybase_fetch_row($row_result);
	  else
       $this->ranges[$result][$i++]=sybase_fetch_row($row_result);
      sybase_free_result($row_result);
     }
	}
    if($result
    && !$this->DoQuery("DEALLOCATE CURSOR select_cursor"))
    {
     Unset($this->ranges[$result]);
     sybase_free_result($result);
     $result=0;
	}
   }
  }
  else
  {
   if ($first==0 && $limit>0)
    $this->DoQuery("SET ROWCOUNT " . $limit);
   $result=$this->DoQuery($query);
  }
  if($result)
  {
   if($select)
    $this->highest_fetched_row[$result]=-1;
   else
    $this->affected_rows=sybase_affected_rows($this->connection);
  }
  else
   $this->SetError("Could not query the Sybase server");
  if ($first==0 && $limit>0)
   $this->DoQuery("SET ROWCOUNT 0");
  return($result);
 }

 Function FetchRow($result,$row)
 {
  $result_value=intval($result);
  if($this->current_row[$result_value]!=$row)
  {
   if(IsSet($this->ranges[$result]))
   {
    if($row>count($this->ranges[$result]))
    {
     $this->last_error="attempted to retrieve a row outside the result set range";
     return(0);
    }
    $this->fetched_row[$result]=$this->ranges[$result][$row];
   }
   else
   {
    if(!sybase_data_seek($result,$row))
    {
     $this->last_error="could not move the result row position";
     return(0);
    }
    if(GetType($this->fetched_row[$result]=sybase_fetch_row($result))!="array")
    {
     $this->current_row[$result_value]=-1;
     $this->last_error="could not fetch the result row";
     return(0);
    }
   }
   $this->current_row[$result_value]=$row;
   $this->highest_fetched_row[$result]=max($this->highest_fetched_row[$result],$row);
  }
  return(1);
 }

 Function GetColumnNames($result)
 {
  if(!IsSet($this->columns[$result]))
  {
   $this->columns[$result]=array();
   for($column=0;@sybase_field_seek($result,$column);$column++)
   {
    $field=sybase_fetch_field($result);
    $this->columns[$result][strtoupper($field->name)]=$column;
   }
  }
 }

 Function GetColumn($result,$field)
 {
  $this->GetColumnNames($result);
  if(GetType($field)=="integer")
  {
   if(($column=$field)<0
   || $column>=count($this->columns[$result]))
   {
    $this->last_error="attempted to fetch an query result column out of range";
    return(-1);
   }
  }
  else
  {
   $name=strtoupper($field);
   if(!IsSet($this->columns[$result][$name]))
   {
    $this->last_error="attempted to fetch an unknown query result column";
    return(-1);
   }
   $column=$this->columns[$result][$name];
  }
  return($column);
 }

 Function EndOfResult($result)
 {
  if(!IsSet($this->highest_fetched_row[$result]))
  {
   $this->last_error="attempted to check the end of an unknown result";
   return(-1);
  }
  return($this->highest_fetched_row[$result]>=$this->NumberOfRows($result)-1);
 }

 Function FetchResult($result,$row,$field)
 {
  if(($column=$this->GetColumn($result,$field))==-1
  || !$this->FetchRow($result,$row))
   return("");
  if(!IsSet($this->fetched_row[$result][$column]))
  {
   $this->last_error="attempted to fetch a NULL result value";
   return("");
  }
  return($this->fetched_row[$result][$column]);
 }

 Function ResultIsNull($result,$row,$field)
 {
  if(($column=$this->GetColumn($result,$field))==-1
  || !$this->FetchRow($result,$row))
   return("");
  return(!IsSet($this->fetched_row[$result][$column]));
 }

 Function FetchBooleanResult($result,$row,$field)
 {
  return($this->ResultIsNull($result,$row,$field) ? "NULL" : !strcmp($this->FetchResult($result,$row,$field),"1"));
 }

 Function NumberOfRows($result)
 {
  return(IsSet($this->ranges[$result]) ? count($this->ranges[$result]) : sybase_num_rows($result));
 }

 Function FreeResult($result)
 {
  UnSet($this->fetched_row[$result]);
  UnSet($this->highest_fetched_row[$result]);
  if(IsSet($this->ranges[$result]))
   UnSet($this->ranges[$result]);
  return(sybase_free_result($result));
 }

 Function GetTextFieldTypeDeclaration($name,&$field)
 {
  return((IsSet($field["length"]) ? "$name VARCHAR (".$field["length"].")" : "$name TEXT").(IsSet($field["default"]) ? " DEFAULT ".$this->GetTextFieldValue($field["default"]) : "").(IsSet($field["notnull"]) ? " NOT NULL" : ""));
 }

 Function GetBooleanFieldTypeDeclaration($name,&$field)
 {
  return("$name BIT".(IsSet($field["default"]) ? " DEFAULT ".$field["default"] : "").(IsSet($field["notnull"]) ? " NOT NULL" : ""));
 }

 Function GetFloatFieldTypeDeclaration($name,&$field)
 {
  return("$name FLOAT".(IsSet($field["default"]) ? " DEFAULT ".$field["default"] : "").(IsSet($field["notnull"]) ? " NOT NULL" : ""));
 }

 Function GetDecimalFieldTypeDeclaration($name,&$field)
 {
  return("$name DECIMAL(18,".$this->decimal_places.")".(IsSet($field["default"]) ? " DEFAULT ".$this->GetDecimalFieldValue($field["default"]) : "").(IsSet($field["notnull"]) ? " NOT NULL" : ""));
 }

 Function GetTextFieldValue($value)
 {
  return("'".AddSlashes($value)."'");
 }

 Function GetBooleanFieldValue($value)
 {
  return(!strcmp($value,"NULL") ? "NULL" : "$value");
 }

 Function GetFloatFieldValue($value)
 {
  return(!strcmp($value,"NULL") ? "NULL" : "$value");
 }

 Function GetDecimalFieldValue($value)
 {
  return(!strcmp($value,"NULL") ? "NULL" : "$value");
 }

 Function CreateSequence($name,$start)
 {
  return($this->Query("CREATE TABLE _sequence_$name (sequence INT NOT NULL IDENTITY($start,1) PRIMARY KEY CLUSTERED)"));
 }

 Function DropSequence($name)
 {
  return($this->Query("DROP TABLE _sequence_$name"));
 }

 Function GetSequenceNextValue($name,&$value)
 {
  if(!$this->Query("INSERT INTO _sequence_$name DEFAULT VALUES")
  || !($result=$this->Query("SELECT @@IDENTITY FROM _sequence_$name")))
   return(0);
  $value=intval($this->FetchResult($result,0,0));
  $this->FreeResult($result);
  if(!$this->Query("DELETE FROM _sequence_$name WHERE sequence<$value"))
   $this->warning="could delete previous sequence table values";
  return(1);
 }

 Function AutoCommitTransactions($auto_commit)
 {
  if(((!$this->auto_commit)==(!$auto_commit)))
   return(1);
  if($this->connection)
  {
   if(!$this->Query($auto_commit ? "COMMIT TRANSACTION" : "BEGIN TRANSACTION"))
    return(0);
  }
  $this->auto_commit=$auto_commit;
  return(1);
 }

 Function CommitTransaction()
 {
  if($this->auto_commit)
  {
   $this->last_error="transaction changes are being auto commited";
   return(0);
  }
  return($this->Query("COMMIT TRANSACTION") && $this->Query("BEGIN TRANSACTION"));
 }

 Function RollbackTransaction()
 {
  if($this->auto_commit)
  {
   $this->last_error="transactions can not be rolled back when changes are auto commited";
   return(0);
  }
  return($this->Query("ROLLBACK TRANSACTION") && $this->Query("BEGIN TRANSACTION"));
 }

 Function Setup()
 {
  $this->supported["AffectedRows"]=
  $this->supported["Indexes"]=
  $this->supported["OrderByText"]=
  $this->supported["Sequences"]=
  $this->supported["SummaryFunctions"]=
  $this->supported["Transactions"]=
  $this->supported["SelectRowRanges"]=
   1;
  return("");
 }

};

}
?>
