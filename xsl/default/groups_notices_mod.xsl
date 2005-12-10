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
<xsl:value-of select="/nrp/group/group.name"/></a> / Notices
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
Group Notices</p>

<p class="options_center">
	<a>
		<xsl:attribute name="href">
			groups_notices_new.php?group_id=<xsl:value-of select="/nrp/group/@id"/>&amp;
			sess_id=<xsl:value-of select="$session_id"/>
		</xsl:attribute>
		Insert a New Notice
	</a>
</p>

<xsl:if test="count(/nrp/notice) > 0">

<table width="90%" border="1">
<tr>
	<th class="sch_title">Notice</th>
	<th class="sch_title">Author</th>
	<th class="sch_title">Date</th>
	<th class="sch_title">Time</th>
	<th class="sch_title">Remove</th>
</tr>
<xsl:for-each select="/nrp/notice">
<xsl:sort select="notice.date"/>
<xsl:variable name="author_id" select="notice.owner"/>
<tr>
	<td><xsl:value-of select="notice.text"/></td>
	<td class="al_center"><xsl:value-of select="/nrp/person[@id=$author_id]/person.name"/></td>
	<td class="al_center"><xsl:value-of select="notice.date"/></td>
	<td class="al_center"><xsl:value-of select="notice.time"/></td>
	<td class="al_center">
		<a>
			<xsl:attribute name="href">
				groups_notices_remove.php?sess_id=<xsl:value-of select="$session_id"/>&amp;notice_id=<xsl:value-of select="@id"/>&amp;group_id=<xsl:value-of select="/nrp/group/@id"/>
			</xsl:attribute>
			<img src="images/button_drop.png" border="0">
				<xsl:attribute name="alt">
					Remove Notice <xsl:value-of select="@id"/>
				</xsl:attribute>
			</img>
		</a>
	</td>
</tr>
</xsl:for-each>
</table>

</xsl:if>
<xsl:if test="count(/nrp/notice) = 0">
	<p class="big_head">There are no notices for this group</p>
</xsl:if>

<br/>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>

</xsl:stylesheet>
