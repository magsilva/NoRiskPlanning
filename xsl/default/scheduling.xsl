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

<div class="location">
     Your location: NRP / <xsl:value-of select="/nrp/schedule/@id"/> /
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$mainurl"/>
</xsl:attribute>
Main
</a>
/ Scheduling
</div>


<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<xsl:variable name="ins_app" select="concat('sch_ins_app.php?sess_id=', $session_id)" />
<xsl:variable name="ins_week_app" select="concat('sch_ins_week_app.php?sess_id=', $session_id)" />
<xsl:variable name="del_app" select="concat('sch_del_app.php?sess_id=', $session_id)" />
<xsl:variable name="del_week_app" select="concat('sch_del_week_app.php?sess_id=', $session_id)" />
<xsl:variable name="mod_app" select="concat('sch_mod_app.php?sess_id=', $session_id)" />
<xsl:variable name="mod_week_app" select="concat('sch_mod_week_app.php?sess_id=', $session_id)" />
<xsl:variable name="cle_app" select="concat('sch_clear_app.php?sess_id=', $session_id)" />
<xsl:variable name="img_sch" select="concat('sch_img.php?sess_id=', $session_id)" />
<xsl:variable name="sch_auth" select="concat('sch_auth.php?sess_id=', $session_id)" />
<xsl:variable name="sch_rooms" select="concat('sch_rooms.php?sess_id=', $session_id)" />
<xsl:variable name="sch_courses" select="concat('sch_courses.php?sess_id=', $session_id)" />

<br />

<table cellpadding="7" summary="Scheduling Options"><tr><td>
<p class="options">
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$ins_app"/>
</xsl:attribute>
Insert New Appointment
</a><br/>
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$ins_week_app"/>
</xsl:attribute>
Insert New Weekly Appointment
</a> </p>
<p class="options">
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$del_app"/>
</xsl:attribute>
Delete Appointment
</a><br/>
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$del_week_app"/>
</xsl:attribute>
Delete Weekly Appointment
</a> </p>
<p class="options">
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$mod_app"/>
</xsl:attribute>
Modify Appointment
</a>
<br/>
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$mod_week_app"/>
</xsl:attribute>
Modify Weekly Appointment
</a></p>

</td>
<td></td>

<td valign="top">

<p class="options">
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$img_sch"/>
</xsl:attribute>
View My Schedule in .PNG format
</a> </p>

<p class="options">
<a>
	<xsl:attribute name="href">
		<xsl:copy-of select="$sch_auth"/>
	</xsl:attribute>
	Manage Unauthorized Appointments
</a>
</p>

<xsl:if test="/nrp/master_session = 0">

<p class="options">
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$cle_app"/>
</xsl:attribute>
Clear all appointments
</a> </p>

<p class="options">
<a>
	<xsl:attribute name="href">
		<xsl:copy-of select="$sch_rooms"/>
	</xsl:attribute>
	Manage Rooms
</a>
</p>

<p class="options">
<a>
	<xsl:attribute name="href">
		<xsl:copy-of select="$sch_courses"/>
	</xsl:attribute>
	Manage Courses
</a>
</p>
</xsl:if>

</td>
</tr></table>


</div>

<div class="down_screen">
</div>


</body>
</html>

</xsl:template>

</xsl:stylesheet>
