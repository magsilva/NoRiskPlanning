<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="adm_menu.xsl"/>

<xsl:include href="schedule.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />
<xsl:variable name="url_main" select="concat('adm_main.php?sess_id=', $session_id)" />


<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US"
xml:lang="en-US">

<head>
	<title>No Risk Planning</title>
	<link rel="stylesheet" type="text/css" href="css/nrp.css" />
</head>

<body>

<xsl:apply-templates select = "nrp/sess_id"/>

<div class="location">
     Your location: NRP / Admin /
     <a>
 	<xsl:attribute name="href">
		<xsl:copy-of select="$url_main"/>
	</xsl:attribute>
     Main</a> / Password
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>
<br/>

<form action="adm_password.php" method="POST">
<table summary ="Password information" class="data">
<tr>
	<th align="left"><label for="current_password">Current Password: </label></th>
	<td align="left"><input type="password" name="current_password" size="20" maxlength="20" />
	</td>
</tr>
<tr>
	<th align="left"><label for="new_password">New Password: </label></th>
	<td align="left">
		<input type="password" name="new_password" size="20" maxlength="20"/>
	</td>
</tr>
<tr>
	<th align="left"><label for="conf_password">Password confirmation: </label></th>
	<td align="left">
		<input type="password" name="conf_password" size="20" maxlength="20"/>
	</td>
</tr>
</table>
<input type="hidden" name="sess_id">
	<xsl:attribute name="value">
		<xsl:value-of select="$session_id"/>
	</xsl:attribute>
</input>

<br/><input type="submit" class="button" name="modify" value="Modify Password"/>

</form>

<br/>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
