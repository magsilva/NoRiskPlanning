<?
if ($cfg['enable_server_transf'])
	$option = 'xsl';
else
	$option = 'xml';

	
do_transformation($result_xml, $result_xsl, $final_result, $option);

/*if ($cfg['enable_server_transf'])
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
        \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">"; */

echo $final_result;
?>
