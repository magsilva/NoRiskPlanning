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
<xsl:variable name="schedule_url" select="concat('scheduling.php?sess_id=', $session_id)" />

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
	<xsl:copy-of select="$schedule_url"/>
</xsl:attribute>
Scheduling
</a>
/ Unauthorized appointments
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<xsl:if test="count(/nrp/schedule/appointment) &gt; 0">

<p class="big_head">
To authorize an appointment, click on the correpondent icon.
</p>

<table width="90%" border="1">
<tr>
	<th class="sch_title">Description</th>
	<th class="sch_title">Day</th>
	<th class="sch_title">Begin</th>
	<th class="sch_title">End</th>
	<th class="sch_title">Type</th>
	<th class="sch_title">Periodicity</th>
	<th class="sch_title">Authorize</th>
	<th class="sch_title">Remove</th>
</tr>
<xsl:for-each select="/nrp/schedule/appointment">
<xsl:sort select="appointment.description"/>
<xsl:variable name="app_id" select="@id"/>
<tr>
	<td class="al_center"><xsl:value-of select="appointment.description"/></td>

	<xsl:if test="appointment.periodicity='common'">
	<td class="al_center">
	<xsl:value-of select="appointment.date/appointment.date.month"/>/<xsl:value-of select="appointment.date/appointment.date.day"/>/<xsl:value-of select="appointment.date/appointment.date.year"/>
	</td>
	</xsl:if>
	<xsl:if test="appointment.periodicity='weekly'">
	<td class="al_center"><xsl:value-of select="appointment.dayofweek"/></td>
	</xsl:if>
	<td class="al_center"><xsl:value-of select="appointment.beg_time"/></td>
	<td class="al_center"><xsl:value-of select="appointment.end_time"/></td>
	<td class="al_center"><xsl:value-of select="appointment.type"/></td>
	<td class="al_center"><xsl:value-of select="appointment.periodicity"/></td>
	<td class="al_center">
		<a>
		<xsl:attribute name="href">
		sch_auth_conf.php?
		sess_id=<xsl:value-of select="$session_id"/>&amp;app_id=<xsl:value-of select="$app_id"/>
		&amp;perio=<xsl:value-of select="appointment.periodicity"/>&amp;conf=yes
		</xsl:attribute>
		<img src="images/ok.png" border="0" width="20">
			<xsl:attribute name="alt">Authorize appointment <xsl:value-of select="appointment.description"/>
			</xsl:attribute>
		</img>
		</a>
	</td>
	<td class="al_center">
		<a>
		<xsl:attribute name="href">
		sch_auth_conf.php?
		sess_id=<xsl:value-of select="$session_id"/>&amp;app_id=<xsl:value-of select="$app_id"/>
		&amp;perio=<xsl:value-of select="appointment.periodicity"/>&amp;conf=no
		</xsl:attribute>
		<img src="images/button_drop.png" border="0">
			<xsl:attribute name="alt">Remove appointment <xsl:value-of select="appointment.description"/>
			</xsl:attribute>
		</img>
		</a>
	</td>
</tr>
</xsl:for-each>
</table>
</xsl:if>

<xsl:if test="count(/nrp/schedule/appointment) = 0">

<p class="big_head">There are no unauthorized appointments.</p>

</xsl:if>

<br/>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>

</xsl:stylesheet>
