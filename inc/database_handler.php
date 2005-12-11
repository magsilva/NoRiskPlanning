<?php
set_magic_quotes_runtime( 0 );

require("./metabase/metabase_parser.php");
require("./metabase/metabase_interface.php");
require("./metabase/metabase_database.php");
require("./metabase/metabase_manager.php");
require("./metabase/metabase_lob.php");

$erro = MetabaseSetupDatabaseObject(array(
	"Type"=>$cfg['db_type'],
	"Host"=>$cfg['server_db'],
	"User"=>$cfg['user_db'],
	"Password"=>$cfg['password_db']
), $bd );

if($erro!="") {
	echo "Database setup error: $error\n";
	exit;
}

$bd->SetDatabase($cfg['bdname']);
?>
