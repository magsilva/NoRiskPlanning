<?php
function do_transformation($xml, $xsl, &$result, $option)
// If option is set to xml, the function will return into result xml
// If option is set to xsl, the function will return the result
// from the transformation of the xml using the specified xsl
{
	if ($option == 'xml')
	{
		header("Content-type: application/xml");
		$result = $xml;
		return 1;
	}
	else if ($option == 'xsl')
	{
		$xsl_proc = xslt_create();
		xslt_set_encoding($xsl_proc, "ISO-8859-1");
		$arg = array('/_xml'=>$xml,);
		$result = xslt_process($xsl_proc, 'arg:/_xml', $xsl, NULL, $arg);
		xslt_free($xsl_proc);
		return 1;
	}
	else
	{
		return 0;
	}
}
?>
