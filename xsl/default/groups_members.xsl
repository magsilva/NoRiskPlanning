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
<xsl:value-of select="/nrp/group/group.name"/></a> / Members
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

<p class="options_center">
Members</p>

<table width="90%" border="1">
<tr>
	<th class="sch_title">Name</th>
	<th class="sch_title">Department</th>
	<th class="sch_title">Membership</th>
	<th class="sch_title">E-mail</th>
</tr>
<xsl:for-each select="/nrp/group/group.member">
<xsl:sort select="group.member.name"/>
<xsl:variable name="member_id" select="@id"/>
<tr>
	<td class="al_center"><xsl:value-of select="group.member.name"/></td>
	<td class="al_center"><xsl:value-of select="/nrp/person[@id=$member_id]/person.department"/></td>
	<td class="al_center">
		<xsl:if test="group.member.membership = 'O'">Owner</xsl:if>
		<xsl:if test="group.member.membership = 'M'">Moderator</xsl:if>
		<xsl:if test="group.member.membership = 'C'">Member</xsl:if>
		<xsl:if test="group.member.membership = 'I'">Not confirmed</xsl:if>
	</td>
	<td class="al_center">
	<xsl:value-of select="/nrp/person[@id=$member_id]/person.email"/></td>
</tr>
</xsl:for-each>
</table>

<br/>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>

</xsl:stylesheet>
