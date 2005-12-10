<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="menu.xsl"/>
<xsl:include href="menu_slave.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />

<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US"
xml:lang="en-US">

<head>
	<title>No Risk Planning</title>
	<link rel="stylesheet" type="text/css" href="css/nrp.css" />
</head>

<body>

<xsl:if test="/nrp/master_session = 0">
	<xsl:apply-templates select = "/nrp/sess_id"/>
</xsl:if>
<xsl:if test="/nrp/master_session != 0">
	<xsl:apply-templates select = "/nrp/master_session"/>
</xsl:if>

<xsl:variable name="mainurl" select="concat('main.php?sess_id=', $session_id)" />
<xsl:variable name="schurl" select="concat('scheduling.php?sess_id=', $session_id)"/>

<div class="location">
     Your location: NRP / <xsl:value-of select="/nrp/schedule/@id"/> /
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$mainurl"/>
</xsl:attribute>
Main
</a>
/ <a>
<xsl:attribute name="href">
	<xsl:copy-of select="$schurl"/>
</xsl:attribute>Scheduling</a> / Modify Appointment
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<form action="sch_mod_app1.php" method="POST">
<p class="big_head">
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

<input type="submit" class="button" name="submit_conf_yes" value=" Yes "/> -
<input type="submit" class="button" name="submit_conf_no" value=" No "/>
</p>
</form>
</div>


<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
