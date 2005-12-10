<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="adm_menu.xsl"/>

<xsl:include href="schedule.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />
<xsl:variable name="url_main" select="concat('adm_main.php?sess_id=', $session_id)" />
<xsl:variable name="departments_url" select="concat('adm_departments.php?sess_id=', $session_id)"/>

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
     Main</a> /
     <a>
	<xsl:attribute name="href">
		<xsl:copy-of select="$departments_url"/>
	</xsl:attribute>
     Departments</a> / Create New Department
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

<form action="adm_departments_new.php" method="POST">
<table summary ="New Department information" class="data">
<tr>
	<th align="left"><label for="name">Department name: </label></th>
	<td align="left"><input type="text" name="name" size="40" maxlength="70" >
		<xsl:attribute name="value">
			<xsl:value-of select="/nrp/department/department.name"/>
		</xsl:attribute>
 	    </input>
	</td>
</tr>
<tr>
	<th align="left"><label for="acronym">Department Acronym: </label></th>
	<td align="left">
		<input type="text" name="acronym" size="8" maxlength="15">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/department/department.acronym"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="description">Description: </label><br/>(optional)</th>
	<td align="left">
		<textarea name="description" rows="3" cols="40">
			<xsl:value-of select="/nrp/department/department.description"/>
		</textarea>
	</td>
</tr>
<tr>
	<th align="left"><label for="unit_id">Unit: </label></th>
	<td align="left">
	<select name="unit_id">
	<xsl:variable name="cur_unit_id" select="/nrp/department/department.unit/@id"/>
		<xsl:for-each select="/nrp/unit">
		<xsl:sort select="unit.name"/>
			<option>
				<xsl:attribute name="value">
					<xsl:value-of select="@id"/>
				</xsl:attribute>
				<xsl:if test="@id = $cur_unit_id">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="unit.name"/>
			</option>
		</xsl:for-each>
	</select>
	</td>
</tr>
</table>
<input type="hidden" name="sess_id">
	<xsl:attribute name="value">
		<xsl:value-of select="$session_id"/>
	</xsl:attribute>
</input>

<br/><input type="submit" class="button" name="create" value="Create New Department"/>

</form>

<br/>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
