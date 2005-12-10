<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="adm_menu.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />
<xsl:variable name="password_url" select="concat('adm_password.php?sess_id=', $session_id)"/>

<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US"
xml:lang="en-US">

<head>
	<title>No Risk Planning</title>
	<link rel="stylesheet" type="text/css" href="css/nrp.css" />
</head>

<body>

<xsl:apply-templates select = "nrp/sess_id"/>

<div class="location">
     Your location: NRP / Admin / Main
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

<p class="options_center">
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$password_url"/>
</xsl:attribute>
Modify Password<br/>
</a>

<p class="alert">
Statistics of the System:
</p>

<p class="big_head">
Number of People: <xsl:value-of select="/nrp/people_number"/><br/>
Number of Courses: <xsl:value-of select="/nrp/courses_number"/><br/>
Number of Rooms: <xsl:value-of select="/nrp/rooms_number"/><br/>
Number of Units: <xsl:value-of select="/nrp/units_number"/><br/>
Number of Departments: <xsl:value-of select="/nrp/departments_number"/><br/>
Number of Categories: <xsl:value-of select="/nrp/categories_number"/><br/>
Number of Groups: <xsl:value-of select="/nrp/groups_number"/><br/>
</p>

</p>

</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
