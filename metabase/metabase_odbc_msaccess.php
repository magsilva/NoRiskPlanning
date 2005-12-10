<?php

if(!defined("METABASE_ODBC_MSACCESS_INCLUDED"))
{
	define("METABASE_ODBC_MSACCESS_INCLUDED",1);

/*
 * metabase_odbc_msaccess.php
 *
 * @(#) $Header: /cvsroot/phpsecurityadm/metabase/metabase_odbc_msaccess.php,v 1.1.1.1 2003/02/27 20:55:34 koivi Exp $
 *
 */

class metabase_odbc_msaccess_class extends metabase_odbc_class
{
	//removed $row argument from odbc_fetch_into calls because of an Access bug
	Function FetchInto($result,$row,&$array)
	{
		if($this->php_version>=4002000)
			return(odbc_fetch_into($result,$array)); 
		elseif($this->php_version>=4000005)
			return(odbc_fetch_into($result,$array));
		else
		{
			eval("\$success=odbc_fetch_into(\$result,&\$array);");
			return($success);
		}
	}

	// If UseDecimalScale=1 - use INT for DECIMAL, otherwise use CURRENCY
	// TODO: issue a warning if decimal_places>4 and UseDecimalScale=0
	Function GetTypeDeclaration($type,$size=0)
	{
		$current_dba_access=$this->dba_access;
		$this->dba_access=0;
		$declaration="";
		$scale=0;
		if(!strcmp($declaration,""))
		{
			switch($type)
			{
				case METABASE_ODBC_VARCHAR_TYPE:
					$declaration="VARCHAR";
					break;
				case METABASE_ODBC_LONGVARCHAR_TYPE:
					$declaration="MEMO";
					break;
				case METABASE_ODBC_BIT_TYPE:
					$declaration="BIT";
					break;
				case METABASE_ODBC_INTEGER_TYPE:
					$declaration="INT";
					break;
				case METABASE_ODBC_DECIMAL_TYPE:
					if($this->support_decimal_scale)
						$declaration="INT";
					else
						$declaration="CURRENCY";
					break;
				case METABASE_ODBC_DATE_TYPE:
					$declaration="DATETIME";
					break;
				case METABASE_ODBC_TIME_TYPE:
					$declaration="DATETIME";
					break;
				case METABASE_ODBC_TIMESTAMP_TYPE:
					$declaration="DATETIME";
					break;
				case METABASE_ODBC_DOUBLE_TYPE:
					$declaration="FLOAT";
					break;
			}
		}
		if($size)
		{
			switch($type)
			{
				case METABASE_ODBC_VARCHAR_TYPE:
					$declaration.="($size)";
					break;
			}
		}
		if($type==METABASE_ODBC_DECIMAL_TYPE)
		{
			if($this->support_decimal_scale)
			{
				$this->decimal_scale=0;
				$this->decimal_factor=($scale<$this->decimal_places ? pow(10.0,$this->decimal_places-$scale) : 1.0);
			}
		}
		$this->dba_access=$current_dba_access;
		return($declaration);
	}

	// Use VARCHAR as the default text type, and LONGCHAR (Memo) only if size>255
	Function GetTextFieldTypeDeclaration($name,&$field)
	{
		if(!IsSet($field["length"]))
			$field["length"]=(IsSet($this->options["DefaultTextFieldLength"]) ? $this->options["DefaultTextFieldLength"] : 255);
		if($field["length"] > 255)
			$declaration=$this->GetTypeDeclaration(METABASE_ODBC_LONGVARCHAR_TYPE);
		else
			$declaration=$this->GetTypeDeclaration(METABASE_ODBC_VARCHAR_TYPE,$field["length"]);
		if(IsSet($field["default"]))
		{
			if($this->support_defaults)
				$declaration.=" DEFAULT ".$this->GetTextFieldValue($field["default"]);
			else
				$this->warning="this ODBC data source does not support field default values";
		}
		return("$name $declaration".(IsSet($field["notnull"]) ? " NOT NULL" : ""));
	}

	// If using CURRENCY, adjust the value because Access always returns 4 decimal places
	// TODO: Consider the consequences of switching between tables or DBs that use the Decimal Scale
	Function GetDecimalFieldValue($value)
	{
		if($this->support_decimal_scale)
		{
			if($this->decimal_scale<0)
				$this->GetTypeDeclaration(METABASE_ODBC_DECIMAL_TYPE,$this->decimal_places);
			return(!strcmp($value,"NULL") ? "NULL" : ($this->decimal_scale<$this->decimal_places ? ($this->decimal_scale>0 ? sprintf("%.".$this->decimal_scale."f",doubleval($value)*$this->decimal_factor) : strval(round(doubleval($value)*$this->decimal_factor))) : strval($value)));
		} else {
			return(!strcmp($value,"NULL") ? "NULL" : number_format(doubleval($value),$this->decimal_places,'.',''));
		}
	}

	// Similar change as above for DECIMAL. Also take only what we need from a DATETIME field
	Function ConvertResult(&$value,$type)
	{
		switch($type)
		{
			case METABASE_TYPE_BOOLEAN:
				$value=(strcmp($value,"1") ? 0 : 1);
				return(1);
			case METABASE_TYPE_DECIMAL:
				if($this->support_decimal_scale)
				{
					if($this->decimal_scale<0)
						$this->GetTypeDeclaration(METABASE_ODBC_DECIMAL_TYPE,$this->support_decimal_scale ? $this->decimal_places : 0);
					if($this->decimal_scale<$this->decimal_places)
						$value=sprintf("%.".$this->decimal_places."f",doubleval($value)/$this->decimal_factor);
				} else {
					$value=number_format(doubleval($value),$this->decimal_places,'.','');
				}
				return(1);
			case METABASE_TYPE_FLOAT:
				$value=doubleval($value);
				return(1);
			case METABASE_TYPE_DATE:
				$value=substr($value,0,10);
				return(1);     
			case METABASE_TYPE_TIME:
				$value=substr($value,11,8);
				return(1);     
			case METABASE_TYPE_TIMESTAMP:
				return(1);
			default:
				return($this->BaseConvertResult($value,$type));
		}
	}

	// If there is ONLY the AUTOINCREMENT field, we have to insert a numeric value into it every time
	// That is fine here, but later we need the 'foo' field to insert NULL values into it for auto-incrementation
	Function CreateSequence($name,$start)
	{
		if($this->Query("CREATE TABLE _sequence_$name (sequence AUTOINCREMENT NOT NULL PRIMARY KEY, foo BIT)"))
			return($this->Query("INSERT INTO _sequence_$name(sequence) VALUES(".(intval($start)-1).")"));
		return(0);
	}

	Function DropSequence($name)
	{
		return($this->Query("DROP TABLE _sequence_$name"));
	}

	// This will only work in Access 2000
	Function GetSequenceNextValue($name,&$value)
	{
		if(!($this->Query("INSERT INTO _sequence_$name(foo) VALUES(NULL)"))
		|| !($result=$this->Query("SELECT @@IDENTITY FROM _sequence_$name")))
			return(0);
		$value=intval($this->FetchResult($result,0,0));
		$this->FreeResult($result);
		if(!$this->Query("DELETE FROM _sequence_$name WHERE sequence<$value"))
			$this->warning="could not delete previous sequence table values";
		return(1);
	}

	Function GetSequenceCurrentValue($name,&$value)
	{
		if(!($result=$this->Query("SELECT sequence FROM _sequence_$name")))
			return(0);
		if($this->NumberOfRows($result)==0)
		{
			$this->FreeResult($result);
			return($this->SetError("Get sequence current value","could not find value in sequence table"));
		}
		$value=intval($this->FetchResult($result,0,0));
		$this->FreeResult($result);
		return(1);
	}

	// Unsetting LOBs because only CLOBs seem to work, but you can use those as text>256 anyway
	// Unsetting Replace because that way Transactions pass the tests (remember to turn-on the
	// transactions in driver_test_config
	Function SetupODBC()
	{
		$this->supported["Indexes"]=
		$this->supported["Sequences"]=
		$this->supported["GetSequenceCurrentValue"]=1;

		unset($this->supported["LOBs"]);
		unset($this->supported["Replace"]);
		return(1);
	}

};

}
?>