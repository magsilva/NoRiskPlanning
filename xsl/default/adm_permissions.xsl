<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="adm_menu.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />
<xsl:variable name="url_main" select="concat('adm_main.php?sess_id=', $session_id)" />
<xsl:variable name="new_perm_room_url" select="concat('adm_permissions_room_new.php?sess_id=', $session_id)"/>
<xsl:variable name="new_perm_course_url" select="concat('adm_permissions_course_new.php?sess_id=', $session_id)"/>

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
     Main</a> / Permissions
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
		<xsl:copy-of select="$new_perm_room_url"/>
	</xsl:attribute>Create New Permission To Room
</a>
</p>

<p class="options_center">
<a>
	<xsl:attribute name="href">
		<xsl:copy-of select="$new_perm_course_url"/>
	</xsl:attribute>Create New Permission To Course
</a>
</p>

<xsl:if test="count(/nrp/permission) = 0">
	<p class="big_head">
		There are no permissions.
	</p>
</xsl:if>

<xsl:if test="count(/nrp/permission) &gt; 0">
<table width="90%" border="1">
<tr>
	<th class="sch_title">Master User</th>
	<th class="sch_title">Master Group</th>
	<th class="sch_title">Master Category</th>
	<th class="sch_title">Slave</th>
	<th class="sch_title">Slave Role</th>
	<th class="sch_title">Remove</th>
</tr>
<xsl:for-each select="/nrp/permission">
<xsl:sort select="permission.slave"/>
<xsl:variable name="permission_id" select="@id"/>
<xsl:variable name="remove_url" select="concat('adm_permissions_remove.php?sess_id=', $session_id)"/>
<tr>
	<td class="al_center">
		<xsl:if test="permission.masterperson != ''">
			<xsl:value-of select="permission.masterperson"/>
		</xsl:if>
		<xsl:if test="permission.masterperson = ''">
			.
		</xsl:if>
	</td>
	<td class="al_center">
		<xsl:if test="permission.mastergroup != ''">
			<xsl:value-of select="permission.mastergroup"/>
		</xsl:if>
		<xsl:if test="permission.mastergroup = ''">
			.
		</xsl:if>
	</td>
	<td class="al_center">
		<xsl:if test="permission.mastercategory != ''">
			<xsl:value-of select="permission.mastercategory"/>
		</xsl:if>
		<xsl:if test="permission.mastercategory = ''">
			.
		</xsl:if>
	</td>
	<td class="al_center"><xsl:value-of select="permission.slave"/></td>
	<td class="al_center"><xsl:value-of select="permission.role"/></td>
	<td class="al_center">
		<a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$remove_url"/>&amp;perm_id=<xsl:value-of select="@id"/>
			</xsl:attribute>
			<img src="images/button_drop.png" border="0">
				<xsl:attribute name="alt">
					Remove permission <xsl:value-of select="@id"/>
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
