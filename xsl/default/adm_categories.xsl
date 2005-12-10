<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="adm_menu.xsl"/>

<xsl:include href="schedule.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />
<xsl:variable name="url_main" select="concat('adm_main.php?sess_id=', $session_id)" />
<xsl:variable name="new_category_url" select="concat('adm_categories_new.php?sess_id=', $session_id)"/>

<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US"
xml:lang="en-US">

<head>
	<title>No Risk Planning</title>
	<link rel="stylesheet" type="text/css" href="css/nrp.css" />
</head>

<body>

<xsl:apply-templates select = "nrp/sess_id"/>

<div class="location">
     Your location: NRP / Admin /
     <a>
 	<xsl:attribute name="href">
		<xsl:copy-of select="$url_main"/>
	</xsl:attribute>
     Main</a> / Categories
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>
<br/>

<p class="options_center">
<a>
	<xsl:attribute name="href">
		<xsl:copy-of select="$new_category_url"/>
	</xsl:attribute>Create New Category
</a>
</p>

<xsl:if test="count(/nrp/category) = 0">
	<p class="big_head">
		There are no categories.
	</p>
</xsl:if>

<xsl:if test="count(/nrp/category) &gt; 0">
<table width="90%" border="1">
<tr>
	<th class="sch_title">Category</th>
	<th class="sch_title">Description</th>
	<th class="sch_title">Modify</th>
	<th class="sch_title">Remove</th>
</tr>
<xsl:for-each select="/nrp/category">
<xsl:sort select="category.name"/>
<xsl:variable name="category_id" select="@id"/>
<xsl:variable name="modify_url" select="concat('adm_categories_modify.php?sess_id=', $session_id)"/>
<xsl:variable name="remove_url" select="concat('adm_categories_remove.php?sess_id=', $session_id)"/>
<tr>
	<td><xsl:value-of select="category.name"/></td>
	<td class="al_center">
		<xsl:if test="category.description != ''">
			<xsl:value-of select="category.description"/>
		</xsl:if>
		<xsl:if test="category.description = ''">
			.
		</xsl:if>
	</td>
	<td class="al_center">
		<a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$modify_url"/>&amp;cat_id=<xsl:value-of select="@id"/>
			</xsl:attribute>
			<img src="images/button_edit.png" border="0">
				<xsl:attribute name="alt">
					Modify Category <xsl:value-of select="category.name"/>
				</xsl:attribute>
			</img>
		</a>
	</td>
	<td class="al_center">
		<a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$remove_url"/>&amp;cat_id=<xsl:value-of select="@id"/>
			</xsl:attribute>
			<img src="images/button_drop.png" border="0">
				<xsl:attribute name="alt">
					Remove Category <xsl:value-of select="category.name"/>
				</xsl:attribute>
			</img>
		</a>
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
