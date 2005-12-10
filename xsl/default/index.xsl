<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US"
xml:lang="en-US">

<head>
	<title>No Risk Planning</title>
	<link rel="stylesheet" type="text/css" href="css/nrp.css" />
</head>

<body>
<div class="top_screen">
	<table width="100%" border="0" summary="Menu options">
	<tr><td>
		<img src="images/nrp_logo.png" alt="No Risk Planning logo" border="0" />
	</td>
	<td></td>
	</tr>
	</table>
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<form method="post" action="index.php">
<p class="big_head">
<br />
Fill in your ID and Password and click on Login
<br />
</p>

<label for="id">ID :</label>
<input type="text" name="id" id="id" size="16" maxlength="32">
<xsl:attribute name="value">
	<xsl:if test="/nrp/schedule/@id = ''">username</xsl:if> 
	<xsl:if test="/nrp/schedule/@id != ''"><xsl:value-of select="/nrp/schedule/@id"/></xsl:if>
</xsl:attribute>
</input>
 <br />
<label for="password">Password :</label> <input type="password" id="password" name="password" size="9" maxlength="12" value=""/> <br />
<br />
<input class="button" type="submit" value="Login" name="submit" id="submit" /><br /><br />
<p class="big_head">If you lost your ID and Password, fill in your e-mail address and click on Send to recover them.<br /></p>
        <label for="email">E-mail: </label><input type="text" id="email" name="email" size="25" maxlength="50" value="e-mail" /> <input class="button" type="submit" value="Send" id="send" name="send" />

</form>
</div>

<div class="down_screen">
</div>

<p align="center">
<a href="http://www.w3.org/WAI/WCAG1AA-Conformance"
      title="Explanation of Level Double-A Conformance">
  <img height="32" width="88"
          src="images/wcag1AA.gif"
          alt="Level Double-A conformance icon,
          W3C-WAI Web Content Accessibility Guidelines 1.0" border="0"/></a>
</p>

</body>
</html>
</xsl:template>

</xsl:stylesheet>
