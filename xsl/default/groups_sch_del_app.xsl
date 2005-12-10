<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="menu.xsl"/>

<xsl:template match="schedule">
<xsl:for-each select="appointment">
<xsl:sort select="appointment.description" />

<option>
<xsl:attribute name="value">
	<xsl:value-of select="@id"/>
</xsl:attribute>

<xsl:value-of select="appointment.description"/> -
<xsl:value-of select="appointment.date/appointment.date.month"/>/
<xsl:value-of select="appointment.date/appointment.date.day"/>/
<xsl:value-of select="appointment.date/appointment.date.year"/> -
<xsl:value-of select="appointment.beg_time"/>
</option>
</xsl:for-each>
</xsl:template>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />

<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US"
xml:lang="en-US">

<head>
	<title>No Risk Planning</title>
	<link rel="stylesheet" type="text/css" href="css/nrp.css" />
</head>

<body>

<xsl:apply-templates select = "nrp/sess_id"/>
<xsl:variable name="mainurl" select="concat('main.php?sess_id=', $session_id)" />
<xsl:variable name="groupsurl" select="concat('groups.php?sess_id=', $session_id)" />
<xsl:variable name="cur_group" select="concat('groups_enter.php?sess_id=', $session_id)" />

<div class="location">
     Your location: NRP /
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$mainurl"/>
</xsl:attribute>
Main
</a>
/ <a>
<xsl:attribute name="href">
	<xsl:value-of select="$groupsurl"/>
</xsl:attribute>
Groups</a> /
<a>
<xsl:attribute name="href">
	<xsl:value-of select="$cur_group"/>&amp;group_id=<xsl:value-of select="/nrp/schedule/appointment/appointment.group/@id"/>
</xsl:attribute>
<xsl:value-of select="/nrp/schedule/appointment/appointment.group"/></a> / Schedule Group Appointment / Delete appointment
</div>


<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<p class="big_head">
Choose the Appointment you want to be deleted and press 'Delete':
</p>
<form action="groups_sch_del_app.php" method="POST">
<p>
<label for="app_id">Appointment: </label>
<select name="app_id">
	<xsl:variable name="test_appointments" select="count(/nrp/schedule/appointment)"/>
	<xsl:if test="$test_appointments != 0">
		<xsl:apply-templates select="/nrp/schedule"/>
	</xsl:if>
</select>
</p>
<p align="center">
<input type="hidden" name="sess_id">
<xsl:attribute name="value">
	<xsl:value-of select="$session_id"/>
</xsl:attribute>
</input>

<input type="hidden" name="group_id">
<xsl:attribute name="value">
	<xsl:value-of select="/nrp/schedule/appointment/appointment.group/@id"/>
</xsl:attribute>
</input>

<input type="submit" class="button" name="submit_choice" value="Delete"/>
</p>
</form>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
