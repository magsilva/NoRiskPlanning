<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">

<html lang="en">

<head>
	<title>No Risk Planning</title>
	<link rel="stylesheet" type="text/css" href="css/nrp.css" />
</head>

<body>
<div class="top_screen">
	<table width="100%" border="0" summary="Menu options">
	<tr><td>
		<img src="images/nrp_logo.png" alt="No Risk Planning logo" border="0" />
	</td>
	<td></td>
	</tr>
	</table>
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<p class="big_head">To return to the login, click on the link below:<br />
<a href="index.php">Return to login</a></p>

</div>

<div class="down_screen">
</div>

</body>
</html>
</xsl:template>

</xsl:stylesheet>
