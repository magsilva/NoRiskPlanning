<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="admin_menu.xsl"/>

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
     Permissions</a> / Remove Permission
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

<form action="adm_permissions_remove.php" method="POST">
<p class="big_head">
Are you sure you want to delete the Permission
to master
<xsl:if test="/nrp/permission/permission.masterperson != ''">
person "<xsl:value-of select="/nrp/permission/permission.masterperson"/>"
</xsl:if>
<xsl:if test="/nrp/permission/permission.mastergroup != ''">
group "<xsl:value-of select="/nrp/permission/permission.mastergroup"/>"
</xsl:if>
<xsl:if test="/nrp/permission/permission.mastercategory != ''">
person "<xsl:value-of select="/nrp/permission/permission.mastercategory"/>"
</xsl:if>
and slave
<xsl:value-of select="/nrp/permission/permission.role"/> "<xsl:value-of select="/nrp/permission/permission.slave"/>" ?
</p>

<p>

<input type="hidden" name="perm_id">
<xsl:attribute name="value">
	<xsl:value-of select="/nrp/permission/@id"/>
</xsl:attribute>
</input>
<input type="hidden" name="sess_id">
<xsl:attribute name="value">
	<xsl:value-of select="$session_id"/>
</xsl:attribute>
</input>

<input type="submit" class="button" name="submit_conf_yes" value=" Yes "/> -
<input type="submit" class="button" name="submit_conf_no" value=" No "/>
</p>
</form>

<br/>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
