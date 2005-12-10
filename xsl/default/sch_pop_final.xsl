<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="menu.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />

<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US"
xml:lang="en-US">

<head>
	<title>NRP</title>
	<link rel="stylesheet" type="text/css" href="css/nrp.css" />
</head>

<body>

<xsl:for-each select="/nrp/error">
	<p class="error_pop">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert_pop"><xsl:value-of select="."/></p>
</xsl:for-each>

<p class="big_head_pop">
	<input type="submit" class="button" name="close" value="Close"
	onclick="javascript: window.opener.location.reload(); window.close()"/>
</p>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
