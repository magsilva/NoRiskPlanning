<?php
        require("./metabase/metabase_parser.php");
        require("./metabase/metabase_manager.php");
        require("./metabase/metabase_database.php");
        require("./metabase/metabase_interface.php");
        require("./metabase/metabase_lob.php");
        require("./metabase/xml_parser.php");

	$erro=MetabaseSetupDatabaseObject(array(

            "Type"=>$cfg['db_type'],
	    "Host"=>$cfg['server_db'],
            "User"=>$cfg['user_db'],
            "Password"=>$cfg['password_db']

	),$bd);
	$bd->SetDatabase($cfg['bdname']);
?>
