<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="sess_id">
	<xsl:variable name="session_id" select="." />
	
	<xsl:variable name="mainurl" select="concat('main.php?sess_id=', $session_id)" />
	<xsl:variable name="schedulingurl" select="concat('scheduling.php?sess_id=', $session_id)" />
	<xsl:variable name="searchurl" select="concat('search.php?sess_id=', $session_id)" />
	<xsl:variable name="profileurl" select="concat('profile.php?sess_id=', $session_id)" />
	<xsl:variable name="groupsurl" select="concat('groups.php?sess_id=', $session_id)" />
	<xsl:variable name="helpurl" select="concat('help.php?sess_id=', $session_id)" />
	<xsl:variable name="logouturl" select="concat('logout.php?sess_id=', $session_id)" />
	
	<div class="top_screen">
		<table width="100%" border="0" summary="Menu options">
		<tr><td width="216">
			<img src="images/nrp_logo.png" alt="No Risk Planning logo" border="0" />
		</td>
		<td>
			<p class="menu">
			<a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$mainurl" />
			</xsl:attribute>
			MAIN
			</a>
			. <a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$schedulingurl" />
			</xsl:attribute>
			SCHEDULING
			</a>
			. <a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$searchurl" />
			</xsl:attribute>
			SEARCH
			</a><br />
			<a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$profileurl" />
			</xsl:attribute>
			PROFILE
			</a>
			. <a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$groupsurl" />
			</xsl:attribute>
			GROUPS
			</a>
			. <a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$helpurl" />
			</xsl:attribute>
			HELP
			</a>
			. <a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$logouturl" />
			</xsl:attribute>
			LOGOUT
			</a>
			</p>
		</td>
		</tr>
		</table>
	</div>
</xsl:template>

</xsl:stylesheet>
