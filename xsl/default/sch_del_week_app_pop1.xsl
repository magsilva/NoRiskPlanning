<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="menu.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />

<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US"
xml:lang="en-US">

<head>
	<title>No Risk Planning</title>
	<link rel="stylesheet" type="text/css" href="css/nrp.css" />
</head>

<body>

<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<form action="sch_del_week_app.php" method="POST">
<p class="big_head_pop">
Are you sure you want to delete the appointment
"<xsl:value-of select="/nrp/schedule/appointment/appointment.description"/>" ?
</p>
<p>

<input type="hidden" name="app_id">
<xsl:attribute name="value">
	<xsl:value-of select="/nrp/schedule/appointment/@id"/>
</xsl:attribute>
</input>
<input type="hidden" name="sess_id">
<xsl:attribute name="value">
	<xsl:value-of select="$session_id"/>
</xsl:attribute>
</input>

<xsl:variable name="number_of_master" select="/nrp/master_sess_id"/>
<xsl:if test="$number_of_master &gt; 0">
	<p align="center"><input type="checkbox" name="master_del"/>
		<label for="master_del">Delete the appointment at this time on master's Schedule</label>
	</p>
</xsl:if>
<input type="hidden" name="is_pop" value="1"/>
</p>

<p class="big_head_pop">
<input type="submit" class="button" name="submit_conf_yes" value=" Yes "/> -
<input type="submit" class="button" name="submit_conf_no" value=" No "/>
</p>
</form>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
