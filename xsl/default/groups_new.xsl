<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="menu.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />
<xsl:variable name="groups_url" select="concat('groups.php?sess_id=', $session_id)"/>

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
     Your location: NRP /
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$mainurl"/>
</xsl:attribute>
Main
</a>
/ <a>
<xsl:attribute name="href"><xsl:value-of select="$groups_url"/></xsl:attribute>
Groups
</a> / Create New Group
</div>
<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>


<form action="groups_new.php" method="POST">
<table summary ="New Group information" class="data">
<tr>
	<th align="left"><label for="name">Name: </label></th>
	<td align="left">
		<input type="text" name="name" size="40" maxlength="100">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/group/group.name"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="category">Category: </label></th>
	<td align="left">
		<input type="text" name="category" size="30" maxlength="40">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/group/group.category"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="acronym">Acronym: </label></th>
	<td align="left">
		<input type="text" name="acronym" size="15" maxlength="15">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/group/group.acronym"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="comments">Description: </label><br/> (optional)</th>
	<td align="left">
		<textarea name="comments" rows="3" cols="40">
			<xsl:value-of select="/nrp/group/group.description"/>
		</textarea>
	</td>
</tr>
</table>
<input type="hidden" name="sess_id">
	<xsl:attribute name="value">
		<xsl:value-of select="$session_id"/>
	</xsl:attribute>
</input>


</form>


<br/>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>

</xsl:stylesheet>
