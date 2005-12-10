<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="adm_menu.xsl"/>

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
<xsl:variable name="mainurl" select="concat('adm_main.php?sess_id=', $session_id)" />

<div class="location">
     Your location: NRP / Admin /
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$mainurl"/>
</xsl:attribute>
Main
</a>
/ Accounts
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<xsl:variable name="people" select="concat('adm_acc_people.php?sess_id=', $session_id)" />
<xsl:variable name="courses" select="concat('adm_acc_courses.php?sess_id=', $session_id)" />
<xsl:variable name="rooms" select="concat('adm_acc_rooms.php?sess_id=', $session_id)" />

<br />
<table cellpadding="7" summary="Accounts Options"><tr><td>
<p class="options">
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$people"/>
</xsl:attribute>
People Management
</a></p>

<p class="options"><a>
<xsl:attribute name="href">
	<xsl:copy-of select="$courses"/>
</xsl:attribute>
Courses Management
</a> </p>
<p class="options">
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$rooms"/>
</xsl:attribute>
Rooms Management
</a>
</p>

</td>
</tr></table>

</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>

</xsl:stylesheet>
