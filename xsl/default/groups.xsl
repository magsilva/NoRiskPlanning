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

<div class="location">
     Your location: NRP / <xsl:value-of select="/nrp/schedule/@id"/> / 
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$mainurl"/>
</xsl:attribute>
Main
</a>
/ Groups 
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<p class="options_center">
<a>
	<xsl:attribute name="href">
		<xsl:copy-of select="$new_group_url"/>
	</xsl:attribute>Create New Group
</a>
</p>

<xsl:if test="count(/nrp/group) = 0">
	<p class="big_head">
		You are not member of any group.
	</p>
</xsl:if>

<xsl:if test="count(/nrp/group) &gt; 0">
<table width="90%" border="1">
<tr>
	<th class="sch_title">Name</th>
	<th class="sch_title">Acronym</th>
	<th class="sch_title">Category</th>
	<th class="sch_title">Description</th>
	<th class="sch_title">My Membership</th>
	<th class="sch_title">Members</th>
</tr>
<xsl:for-each select="/nrp/group">
<xsl:sort select="group.name"/>
<xsl:variable name="group_id" select="@id"/>
<tr>
	<td class="al_center">
	<a>
		<xsl:attribute name="href">
			groups_enter.php?sess_id=<xsl:value-of select="$session_id"/>&amp;group_id=
			<xsl:value-of select="$group_id"/>
		</xsl:attribute>
	<xsl:value-of select="group.name"/>
	</a>
	</td>
	<td class="al_center"><xsl:value-of select="group.acronym"/></td>
	<td class="al_center">
		<xsl:if test="group.category != ''">
			<xsl:value-of select="group.category"/>
		</xsl:if>
		<xsl:if test="group.category = ''">
			.
		</xsl:if>
	</td>
	<td class="al_center">
		<xsl:if test="group.description != ''">
			<xsl:value-of select="group.description"/>
		</xsl:if>
		<xsl:if test="group.description = ''">
			.
		</xsl:if>
	</td>
	<td class="al_center">
		<xsl:variable name="membership" select="group.member[@id=/nrp/schedule/@id]/group.member.membership"/>
		<xsl:if test="$membership='O'">Owner</xsl:if>
		<xsl:if test="$membership='M'">Moderator</xsl:if>
		<xsl:if test="$membership='I'">Not Confirmed</xsl:if>
		<xsl:if test="$membership='C'">Member</xsl:if>
	</td>
	<td class="appointment_month">
		<xsl:for-each select="group.member/group.member.name">
		<xsl:sort select="."/>
			- <xsl:value-of select="."/><br/>
		</xsl:for-each>
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
