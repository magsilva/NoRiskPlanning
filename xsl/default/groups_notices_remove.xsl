<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="menu.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />
<xsl:variable name="new_group_url" select="concat('groups_new.php?sess_id=', $session_id)"/>

<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US"
xml:lang="en-US">

<head>
	<title>No Risk Planning</title>
	<link rel="stylesheet" type="text/css" href="css/nrp.css" />
</head>

<body>

<xsl:apply-templates select = "nrp/sess_id"/>
<xsl:variable name="mainurl" select="concat('main.php?sess_id=', $session_id)" />
<xsl:variable name="groupsurl" select="concat('groups.php?sess_id=', $session_id)" />
<xsl:variable name="cur_group" select="concat('groups_enter.php?sess_id=', $session_id)" />
<xsl:variable name="notices_url" select="concat('groups_notices.php?sess_id=', $session_id)" />

<div class="location">
     Your location: NRP /
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$mainurl"/>
</xsl:attribute>
Main
</a>
/ <a>
<xsl:attribute name="href">
	<xsl:value-of select="$groupsurl"/>
</xsl:attribute>
Groups</a> /
<a>
<xsl:attribute name="href">
	<xsl:value-of select="$cur_group"/>&amp;group_id=<xsl:value-of select="/nrp/group/@id"/>
</xsl:attribute>
<xsl:value-of select="/nrp/group/group.name"/></a> /
<a>
<xsl:attribute name="href">
	<xsl:value-of select="$notices_url"/>&amp;group_id=<xsl:value-of select="/nrp/group/@id"/>
</xsl:attribute>
Notices</a> /

Remove a notice
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<p class="big_head"><xsl:value-of select="/nrp/group/group.name"/> - <xsl:value-of select="/nrp/group/group.acronym"/></p>
<p class="description_center"><xsl:value-of select="/nrp/group/group.description"/></p>

<p class="big_head">
Are you sure you want to remove the notice <xsl:value-of select="/nrp/notice/text"/> ?
</p>


<form action="groups_notices_remove.php" method="post">
<p class="big_head">
<input type="hidden" name="notice_id">
	<xsl:attribute name="value"><xsl:value-of select="/nrp/notice/@id"/></xsl:attribute>
</input>
<input type="hidden" name="group_id">
	<xsl:attribute name="value"><xsl:value-of select="/nrp/group/@id"/></xsl:attribute>
</input>
<input type="hidden"  name="sess_id">
	<xsl:attribute name="value"><xsl:value-of select="$session_id"/></xsl:attribute>
</input>
<br/>
<input type="submit" class="button" name="submit_conf_yes" value="Yes"/> -
<input type="submit" class="button" name="submit_conf_no" value="No"/>
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
