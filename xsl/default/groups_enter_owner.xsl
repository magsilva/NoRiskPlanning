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

<div class="location">
     Your location: NRP / <xsl:value-of select="/nrp/schedule/@id"/> /
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
Groups</a> / <xsl:value-of select="/nrp/group/group.name"/>
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


<table width="60%" border="0" summary="Group functionalities and notices">
<tr>

<td width="50%" valign="top">

<p class="big_head"><a>
<xsl:attribute name="href">
groups_members.php?sess_id=<xsl:value-of select="$session_id"/>&amp;group_id=
<xsl:value-of select="/nrp/group/@id"/>
</xsl:attribute>
Members
</a></p>

<p class="big_head">
<a>
	<xsl:attribute name="href">
		groups_documents.php?group_id=<xsl:value-of select="/nrp/group/@id"/>&amp;
		sess_id=<xsl:value-of select="$session_id"/>
	</xsl:attribute>
	Documents
</a>
</p>

<p class="big_head">
<a>
	<xsl:attribute name="href">
		groups_remove.php?group_id=<xsl:value-of select="/nrp/group/@id"/>&amp;
		sess_id=<xsl:value-of select="$session_id"/>
	</xsl:attribute>
	Remove this group
</a>
</p>

<p class="big_head">
<a>
	<xsl:attribute name="href">
		groups_schedule.php?group_id=<xsl:value-of select="/nrp/group/@id"/>&amp;
		sess_id=<xsl:value-of select="$session_id"/>
	</xsl:attribute>
	Schedule group appointments
</a>
</p>

<p class="big_head">
<a>
	<xsl:attribute name="href">
		groups_members_schedule.php?group_id=<xsl:value-of select="/nrp/group/@id"/>&amp;
		sess_id=<xsl:value-of select="$session_id"/>
	</xsl:attribute>
	Schedule of all members
</a>
</p>

</td>

<td width="50%" valign="top">
<p class="big_head">
Last Notices:
</p>
<p>
<xsl:for-each select="/nrp/notice">
<xsl:sort select="notice.date"/>
<xsl:variable name="author_id" select="notice.owner"/> -
<xsl:value-of select="notice.date"/>-<xsl:value-of select="notice.time"/>
<xsl:value-of select="substring-before(/nrp/person[@id=$author_id]/person.name, ' ')"/> :
<xsl:value-of select="notice.text"/><br/>
</xsl:for-each>
</p>
<p class="small_head">
<a>
	<xsl:attribute name="href">
		groups_notices.php?group_id=<xsl:value-of select="/nrp/group/@id"/>&amp;
		sess_id=<xsl:value-of select="$session_id"/>
	</xsl:attribute>
	All notices<br/>
</a>
	<a>
		<xsl:attribute name="href">
			groups_notices_new.php?group_id=<xsl:value-of select="/nrp/group/@id"/>&amp;
			sess_id=<xsl:value-of select="$session_id"/>
		</xsl:attribute>
		Insert a New Notice
	</a>
</p>

</td>
</tr>

</table>


<br/>

</div>


<div class="down_screen">
</div>

</body>
</html>

</xsl:template>

</xsl:stylesheet>
