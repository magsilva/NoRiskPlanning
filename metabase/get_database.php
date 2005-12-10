<?
/*
 * get_database.php
 *
 * @(#) $Header: /cvsroot/phpsecurityadm/metabase/get_database.php,v 1.1.1.1 2003/02/27 20:55:09 koivi Exp $
 *
 */

	require("metabase_parser.php");
	require("metabase_manager.php");
	require("metabase_database.php");
	require("metabase_interface.php");
	require("xml_parser.php");

Function Dump($output)
{
	echo $output;
}


	if($argc<2)
	{
		echo "Usage:   ".$argv[0]." Connection-string\n";
		echo "Example: ".$argv[0]." mysql://root@localhost/driver_test?Options/Port=/var/lib/mysql/mysql.sock\n";
		exit;
	}
	$arguments=array(
	  "Connection"=>$argv[1]
	);
	$manager=new metabase_manager_class;
	$manager->debug="Output";
	if(strlen($error=$manager->SetupDatabase($arguments))==0)
	{
		if(strlen($error=$manager->GetDefinitionFromDatabase())==0)
		{
			$error=$manager->DumpDatabase(array(
				"Output"=>"Dump",
				"EndOfLine"=>"\n")
			);
		}
	}
	if(strlen($error))
		echo "Error: $error\n";
	if(count($manager->warnings)>0)
		echo "WARNING:\n",implode($manager->warnings,"!\n"),"\n";
	if($manager->database)
	{
		echo MetabaseDebugOutput($manager->database);
		$manager->CloseSetup();
	}
?>
