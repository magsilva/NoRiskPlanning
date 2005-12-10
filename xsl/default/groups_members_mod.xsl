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

<p class="big_head">
Members</p>

<p class="options_center">
	<a>
		<xsl:attribute name="href">
			groups_members_new.php?group_id=<xsl:value-of select="/nrp/group/@id"/>&amp;
			sess_id=<xsl:value-of select="$session_id"/>
		</xsl:attribute>
		Invite A New Member
	</a>
</p>
<table width="90%" border="1">
<tr>
	<th class="sch_title">Name</th>
	<th class="sch_title">Department</th>
	<th class="sch_title">Membership</th>
	<th class="sch_title">E-mail</th>
	<th class="sch_title">Modify</th>
	<th class="sch_title">Remove</th>
</tr>
<xsl:for-each select="/nrp/group/group.member">
<xsl:sort select="group.member.name"/>
<xsl:variable name="member_id" select="@id"/>
<xsl:variable name="modify_url" select="concat('groups_members_modify.php?sess_id=', $session_id)"/>
<xsl:variable name="remove_url" select="concat('groups_members_remove.php?sess_id=', $session_id)"/>
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
	<xsl:if test="group.member.membership != 'O'">
	<td align="center">
		<a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$modify_url"/>&amp;account_id=<xsl:value-of select="@id"/>&amp;group_id=<xsl:value-of select="/nrp/group/@id"/>
			</xsl:attribute>
			<img src="images/button_edit.png" border="0">
				<xsl:attribute name="alt">
					Modify member <xsl:value-of select="@id"/>
				</xsl:attribute>
			</img>
		</a>
	</td>
	<td class="al_center">
		<a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$remove_url"/>&amp;account_id=<xsl:value-of select="@id"/>&amp;group_id=<xsl:value-of select="/nrp/group/@id"/>
			</xsl:attribute>
			<img src="images/button_drop.png" border="0">
				<xsl:attribute name="alt">
					Remove Member <xsl:value-of select="@id"/>
				</xsl:attribute>
			</img>
		</a>
	</td>
	</xsl:if>
	<xsl:if test="group.member.membership = 'O'">
		<td colspan="2">.</td>
	</xsl:if>
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
