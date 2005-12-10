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

<form action="sch_mod_app1.php" method="POST">
<p class="big_head_pop">
There are other weekly appointments scheduled within the informed time span. <br />Do you want to overlap this (these) appointment(s) to schedule the appointment "<xsl:value-of select="/nrp/schedule/appointment/appointment.description"/>" ?
</p>
<p>

<input type="hidden" name="description">
<xsl:attribute name="value">
	<xsl:value-of select="/nrp/schedule/appointment/appointment.description"/>
</xsl:attribute>
</input>
<input type="hidden" name="type">
<xsl:attribute name="value">
	<xsl:value-of select="/nrp/schedule/appointment/appointment.type/@id"/>
</xsl:attribute>
</input>
<input type="hidden" name="day">
<xsl:attribute name="value">
	<xsl:value-of select="/nrp/schedule/appointment/appointment.date/appointment.date.day"/>
</xsl:attribute>
</input>
<input type="hidden" name="month">
<xsl:attribute name="value">
	<xsl:value-of select="/nrp/schedule/appointment/appointment.date/appointment.date.month"/>
</xsl:attribute>
</input>
<input type="hidden" name="year">
<xsl:attribute name="value">
	<xsl:value-of select="/nrp/schedule/appointment/appointment.date/appointment.date.year"/>
</xsl:attribute>
</input>
<input type="hidden" name="beg_time">
<xsl:attribute name="value">
	<xsl:value-of select="/nrp/schedule/appointment/appointment.beg_time/@id"/>
</xsl:attribute>
</input>
<input type="hidden" name="end_time">
<xsl:attribute name="value">
	<xsl:value-of select="/nrp/schedule/appointment/appointment.end_time/@id"/>
</xsl:attribute>
</input>
<input type="hidden" name="url">
<xsl:attribute name="value">
	<xsl:value-of select="/nrp/schedule/appointment/appointment.url"/>
</xsl:attribute>
</input>
<input type="hidden" name="app_id">
<xsl:attribute name="value">
	<xsl:value-of select="/nrp/schedule/appointment/@id"/>
</xsl:attribute>
</input>
<input type="hidden" name="ins_at_master">
<xsl:attribute name="value">
	<xsl:value-of select="/nrp/ins_at_master"/>
</xsl:attribute>
</input>

<input type="hidden" name="sess_id">
<xsl:attribute name="value">
	<xsl:value-of select="$session_id"/>
</xsl:attribute>
</input>

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
