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
</xsl:attribute>Scheduling</a> / Delete Weekly Appointment
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<form action="sch_del_week_app.php" method="POST">
<p class="big_head">
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
