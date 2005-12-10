<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="adm_menu.xsl"/>

<xsl:include href="schedule.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />
<xsl:variable name="url_main" select="concat('adm_main.php?sess_id=', $session_id)" />
<xsl:variable name="permissions_url" select="concat('adm_permissions.php?sess_id=', $session_id)"/>

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
		<xsl:copy-of select="$permissions_url"/>
	</xsl:attribute>
     Permissions</a> / Create New Permission To Course
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

<form action="adm_permissions_course_new.php" method="POST">
<table summary ="New Permission information" class="data">
<tr>
	<th align="left"><label for="master_id">Master Person: </label></th>
	<td align="left">
		<select name="master_person">
		<option value="">
		<xsl:if test="/nrp/permission/permission.masterperson = ''">
			<xsl:attribute name="selected">selected</xsl:attribute>
		</xsl:if>Choose a Master Person
		</option>
		<xsl:for-each select="/nrp/person">
		<xsl:variable name="cur_person" select="/nrp/permission/permission.masterperson/@id"/>
			<option>
				<xsl:attribute name="value">
					<xsl:value-of select="@id"/>
				</xsl:attribute>
				<xsl:if test="@id = $cur_person">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
			<xsl:value-of select="person.name"/>
			</option>
		</xsl:for-each>
		</select>
	</td>
</tr>
<tr>
	<th align="left"><label for="master_id">Master Group: </label></th>
	<td align="left">
		<select name="master_group">
		<option value="">
		<xsl:if test="/nrp/permission/permission.mastergroup = ''">
			<xsl:attribute name="selected">selected</xsl:attribute>
		</xsl:if>Choose a Master Group
		</option>
		<xsl:for-each select="/nrp/group">
		<xsl:variable name="cur_group" select="/nrp/permission/permission.mastergroup/@id"/>
			<option>
				<xsl:attribute name="value">
					<xsl:value-of select="@id"/>
				</xsl:attribute>
				<xsl:if test="@id = $cur_group">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
			<xsl:value-of select="group.name"/>
			</option>
		</xsl:for-each>
		</select>
	</td>
</tr>
<tr>
	<th align="left"><label for="master_category">Master Category: </label></th>
	<td align="left">
		<select name="master_category">
		<option value="">
		<xsl:if test="/nrp/permission/permission.mastercategory = ''">
			<xsl:attribute name="selected">selected</xsl:attribute>
		</xsl:if>Choose a Master Category
		</option>
		<xsl:for-each select="/nrp/category">
		<xsl:variable name="cur_category" select="/nrp/permission/permission.mastercategory/@id"/>
			<option>
				<xsl:attribute name="value">
					<xsl:value-of select="@id"/>
				</xsl:attribute>
				<xsl:if test="@id = $cur_category">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
			<xsl:value-of select="category.name"/>
			</option>
		</xsl:for-each>
		</select>
	</td>
</tr>
<tr>
	<th align="left"><label for="slave_id">Slave Course: </label></th>
	<td align="left">
		<select name="slave_id">
		<option value="">
		<xsl:if test="/nrp/permission/permission.slave = ''">
			<xsl:attribute name="selected">selected</xsl:attribute>
		</xsl:if>Choose a Slave Course
		</option>
		<xsl:for-each select="/nrp/course">
		<xsl:variable name="cur_course" select="/nrp/permission/permission.slave/@id"/>
			<option>
				<xsl:attribute name="value">
					<xsl:value-of select="@id"/>
				</xsl:attribute>
				<xsl:if test="@id = $cur_course">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
			<xsl:value-of select="course.name"/>
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

<br/><input type="submit" class="button" name="create" value="Create New Permission To Course"/>

</form>

<br/>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
