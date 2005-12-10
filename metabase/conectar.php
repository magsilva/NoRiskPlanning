<?php
	include "./config/config.inc.php";

        require("metabase_parser.php");
        require("metabase_manager.php");
        require("metabase_database.php");
        require("metabase_interface.php");
        require("metabase_lob.php");
        require("xml_parser.php");

      $erro=MetabaseSetupDatabaseObject(array(

            "Type"=>$cfg['db_type'],
	    "Host"=>$cfg['server_db'],
            "User"=>$cfg['user_db'],
            "Password"=>$cfg['password_db']

      ),$bd);
      if($erro!="")
      {

            echo "Erro de configuração da base de dados: $erro\n";
            exit;

      }
      $bd->SetDatabase($base_de_dados,$cfg['bd_name']);
?>
