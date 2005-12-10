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

<xsl:apply-templates select = "nrp/sess_id"/>
<xsl:variable name="mainurl" select="concat('main.php?sess_id=', $session_id)" />
<xsl:variable name="schedurl" select="concat('scheduling.php?sess_id=', $session_id)" />

<div class="location">
     Your location: NRP /
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$mainurl"/>
</xsl:attribute>
Main
</a>
/ 
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$schedurl"/>
</xsl:attribute>
Scheduling</a> / Courses
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<xsl:if test="count(/nrp/course) = 0">
	<p class="big_head">
		There are no courses.
	</p>
</xsl:if>

<xsl:if test="count(/nrp/course) &gt; 0">

<p class="big_head">
To enter a course's schedule, click on its code
</p>

<table width="90%" border="1">
<tr>
	<th class="sch_title">Account_id</th>
	<th class="sch_title">Name</th>
	<th class="sch_title">Code</th>
	<th class="sch_title">Group</th>
	<th class="sch_title">Year</th>
	<th class="sch_title">Semester</th>
	<th class="sch_title">Lecturer</th>
</tr>
<xsl:for-each select="/nrp/course">
<xsl:sort select="course.name"/>
<xsl:variable name="course_id" select="@id"/>
<tr>
	<td align="center">
	<a>
	<xsl:attribute name="href">
		slave.php?sess_id=<xsl:value-of select="$session_id"/>&amp;slave=<xsl:value-of select="@id"/>
	</xsl:attribute>
	<xsl:value-of select="@id"/></a></td>
	<td align="center"><xsl:value-of select="course.name"/></td>
	<td align="center"><xsl:value-of select="course.code"/></td>
	<td align="center"><xsl:value-of select="course.group"/></td>
	<td align="center"><xsl:value-of select="course.year"/></td>
	<td align="center"><xsl:value-of select="course.semester"/></td>
	<td align="center"><xsl:value-of select="course.lecturer"/></td>
</tr>
</xsl:for-each>
</table><br/>
</xsl:if>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>

</xsl:stylesheet>
