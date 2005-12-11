<?php

require_once("./inc/config.inc.php");
require_once(SMARTY_DIR."Smarty.class.php");

class NoRiskSmarty extends Smarty
{
    /**
     * Executes & returns or displays the template results. It will use NRP's directory
     * as base if the $resource_name is a relative path.
     *
     * @param string $resource_name
     * @param string $cache_id
     * @param string $compile_id
     * @param boolean $display
     */
    function fetch($resource_name, $cache_id = null, $compile_id = null, $display = false)
    {
        global $cfg;

        if ($resource_name[0] != "/") {
            if ( $cfg['directory'][strlen($cfg['directory'])-1] != "/") {
                $resource_name = $cfg['directory'] . "/" . $resource_name;
            }
            else {
                $resource_name = $cfg['directory'] . $resource_name;
            } 
        }
        return Smarty::fetch($resource_name, $cache_id, $compile_id, $display);
    }
}
?>
