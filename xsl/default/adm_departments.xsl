<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="adm_menu.xsl"/>

<xsl:include href="schedule.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />
<xsl:variable name="url_main" select="concat('adm_main.php?sess_id=', $session_id)" />
<xsl:variable name="new_department_url" select="concat('adm_departments_new.php?sess_id=', $session_id)"/>

<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US"
xml:lang="en-US">

<head>
	<title>No Risk Planning</title>
	<link rel="stylesheet" type="text/css" href="css/nrp.css" />
</head>

<body>

<xsl:apply-templates select = "nrp/sess_id"/>

<div class="location">
     Your location: NRP / Admin /
     <a>
 	<xsl:attribute name="href">
		<xsl:copy-of select="$url_main"/>
	</xsl:attribute>
     Main</a> / Departments
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
		<xsl:copy-of select="$new_department_url"/>
	</xsl:attribute>Create New Department
</a>
</p>

<xsl:if test="count(/nrp/unit) = 0">
	<p class="big_head">
		There are no departments.
	</p>
</xsl:if>

<xsl:if test="count(/nrp/unit) &gt; 0">
<table width="90%" border="1">
<tr>
	<th class="sch_title">Department</th>
	<th class="sch_title">Acronym</th>
	<th class="sch_title">Description</th>
	<th class="sch_title">Unit</th>
	<th class="sch_title">Modify</th>
	<th class="sch_title">Remove</th>
</tr>
<xsl:for-each select="/nrp/department">
<xsl:sort select="department.name"/>
<xsl:variable name="department_id" select="@id"/>
<xsl:variable name="modify_url" select="concat('adm_departments_modify.php?sess_id=', $session_id)"/>
<xsl:variable name="remove_url" select="concat('adm_departments_remove.php?sess_id=', $session_id)"/>
<xsl:variable name="unit_id" select="department.unit/@id"/>
<tr>
	<td><xsl:value-of select="department.name"/></td>
	<td align="center">
		<xsl:if test="department.acronym != ''">
			<xsl:value-of select="department.acronym"/>
		</xsl:if>
		<xsl:if test="department.acronym = ''">
			.
		</xsl:if>
	</td>
	<td align="center">
		<xsl:if test="department.description != ''">
			<xsl:value-of select="department.description"/>
		</xsl:if>
		<xsl:if test="department.description = ''">
			.
		</xsl:if>
	</td>
	<td align="center">
		<xsl:value-of select="/nrp/unit[@id = $unit_id]/unit.acronym"/>
	</td>
	<td align="center">
		<a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$modify_url"/>&amp;dep_id=<xsl:value-of select="@id"/>
			</xsl:attribute>
			<img src="images/button_edit.png" border="0">
				<xsl:attribute name="alt">
					Modify Unit <xsl:value-of select="unit.acronym"/>
				</xsl:attribute>
			</img>
		</a>
	</td>
	<td align="center">
		<a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$remove_url"/>&amp;dep_id=<xsl:value-of select="@id"/>
			</xsl:attribute>
			<img src="images/button_drop.png" border="0">
				<xsl:attribute name="alt">
					Remove Unit <xsl:value-of select="unit.acronym"/>
				</xsl:attribute>
			</img>
		</a>
	</td>
</tr>
</xsl:for-each>
</table>
</xsl:if>

<br/>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
