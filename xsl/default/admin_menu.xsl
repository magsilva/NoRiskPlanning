<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="sess_id">

<xsl:variable name="session_id" select="." />

<xsl:variable name="main_url" select="concat('adm_main.php?sess_id=', $session_id)" />
<xsl:variable name="accounts_url" select="concat('adm_accounts.php?sess_id=', $session_id)" />
<xsl:variable name="category_url" select="concat('adm_categories.php?sess_id=', $session_id)" />
<xsl:variable name="department_url" select="concat('adm_departments.php?sess_id=', $session_id)" />
<xsl:variable name="branch_url" select="concat('adm_branchs.php?sess_id=', $session_id)" />
<xsl:variable name="helpurl" select="concat('adm_help.php?sess_id=', $session_id)" />
<xsl:variable name="password_url" select="concat('adm_password.php?sess_id=', $session_id)" />
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
			<xsl:copy-of select="$main_url" />
		</xsl:attribute>
		MAIN
		</a>
		  . <a>
		<xsl:attribute name="href">
			<xsl:copy-of select="$accounts_url" />
		</xsl:attribute>
		ACCOUNTS
		</a>
		. <a>
		<xsl:attribute name="href">
			<xsl:copy-of select="$category_url" />
		</xsl:attribute>
		CATEGORIES
		</a>
		. <a>
		<xsl:attribute name="href">
			<xsl:copy-of select="$helpurl" />
		</xsl:attribute>
		HELP
		</a>
		<br />
		<a>
		<xsl:attribute name="href">
			<xsl:copy-of select="$department_url" />
		</xsl:attribute>
		DEPARTMENTS
		</a>
		  . <a>
		<xsl:attribute name="href">
			<xsl:copy-of select="$branch_url" />
		</xsl:attribute>
		BRANCHS
		</a>
		  . <a>
		<xsl:attribute name="href">
			<xsl:copy-of select="$password_url" />
		</xsl:attribute>
		PASSWORD
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
