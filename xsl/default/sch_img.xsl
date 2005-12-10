<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="menu.xsl"/>
<xsl:include href="menu_slave.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />

<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US"
xml:lang="en-US">

<head>
	<title>No Risk Planning</title>
	<link rel="stylesheet" type="text/css" href="css/nrp.css" />
</head>

<body>

<xsl:if test="/nrp/master_session = 0">
	<xsl:apply-templates select = "/nrp/sess_id"/>
</xsl:if>
<xsl:if test="/nrp/master_session != 0">
	<xsl:apply-templates select = "/nrp/master_session"/>
</xsl:if>

<xsl:variable name="mainurl" select="concat('main.php?sess_id=', $session_id)" />
<xsl:variable name="schedulingurl" select="concat('scheduling.php?sess_id=', $session_id)" />

<div class="location">
     Your location: NRP / <xsl:value-of select="/nrp/schedule/@id"/> /
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$mainurl"/>
</xsl:attribute>
Main
</a>
/
<a>
<xsl:attribute name="href">
	<xsl:value-of select="$schedulingurl"/>
</xsl:attribute>
Scheduling</a>
 / Schedule Image
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<p class="title">You can visualize your weekly schedule as a .PNG image. By using it, you can download it
to your disk, send it by e-mail, or display it at your home page.</p>
<p class="title">To show it in your home page, copy the HTML code below: </p>

<textarea cols="100" rows="2">
<img>
<xsl:attribute name="src">
	<xsl:value-of select="/nrp/path"/>image_week.png.php?account_id=<xsl:value-of select="/nrp/schedule/@id"/>
</xsl:attribute>
<xsl:attribute name="alt">Schedule image</xsl:attribute>
</img>
</textarea>
<br />
<br />
<img>
<xsl:attribute name="src">
	image_week.png.php?account_id=<xsl:value-of select="/nrp/schedule/@id"/>
</xsl:attribute>
<xsl:attribute name="alt">
	Schedule image of user <xsl:value-of select="/nrp/schedule/@id"/>
</xsl:attribute>
</img>
<br/><br/>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>

</xsl:stylesheet>
