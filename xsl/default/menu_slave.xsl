<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<xsl:template match="master_session">
	
	<div class="top_screen">
		<table width="100%" border="0" summary="Menu options">
		<tr><td width="216">
			<img src="images/nrp_logo.png" alt="No Risk Planning logo" border="0" />
		</td>
		<td>
			<p class="menu">
			<a>
			<xsl:attribute name="href">main.php?sess_id=<xsl:value-of select="."/></xsl:attribute>
			MAIN
			</a>
			. <a>
			<xsl:attribute name="href">scheduling.php?sess_id=<xsl:value-of select="."/></xsl:attribute>
			SCHEDULING
			</a><br />
			<a>
			<xsl:attribute name="href">search.php?sess_id=<xsl:value-of select="."/></xsl:attribute>
			SEARCH
			</a>
			. <a>
			<xsl:attribute name="href">help.php?sess_id=<xsl:value-of select="."/></xsl:attribute>
			HELP
			</a>
			. <a>
			<xsl:attribute name="href">logout.php?sess_id=<xsl:value-of select="."/></xsl:attribute>
			RETURN
			</a>
			</p>
		</td>
		</tr>
		</table>
	</div>

</xsl:template>

</xsl:stylesheet>
