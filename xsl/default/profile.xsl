<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="menu.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />

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
Your location: NRP / <xsl:value-of select="/nrp/user/@id"/> / 
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$mainurl"/>
</xsl:attribute>Main
</a>
/ Profile
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>
<br />
<form action="profile.php" method="POST">
<table summary ="User Profile Data" class="data">
<tr>
	<th align="left"><label for="name">Name: </label></th>
	<td align="left"><input type="text" name="name" size="40" maxlength="70" >
		<xsl:attribute name="value">
			<xsl:value-of select="/nrp/user/name"/>
		</xsl:attribute>
 	    </input>
	</td>
</tr>
<tr>
	<th align="left"><label>Department: </label></th>
	<td align="left">
		<xsl:value-of select="/nrp/user/user_dep"/>
	</td>
</tr>
<tr>
	<th align="left"><label>Category: </label></th>
	<td align="left">
		<xsl:value-of select="/nrp/user/category"/>
	</td>
</tr>
<tr>
	<th align="left"><label for="email">E-mail: </label></th>
	<td align="left">
		<input type="text" name="email" size="40" maxlength="70">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/user/email"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="url">URL: </label> (optional)</th>
	<td align="left">
		<input type="text" name="url" size="40" maxlength="70">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/user/url"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="commentaries">Comments: </label><br/>(optional)</th>
	<td align="left">
		<textarea name="commentaries" rows="3" cols="40">
			<xsl:value-of select="/nrp/user/comments"/>
		</textarea>
	</td>
</tr>
<tr>
	<th align="left">Appointment types to <br />be shown at schedule image: </th>
	<td align="left">
		<xsl:for-each select="/nrp/user/image_enable">
			<xsl:variable name="checking" select="."/>
			<input type="checkbox">
				<xsl:attribute name="name">
					<xsl:value-of select="@type"/>
				</xsl:attribute>
				<xsl:if test="$checking=1">
					<xsl:attribute name="checked">checked</xsl:attribute>
				</xsl:if>
			</input>
			<label>
				<xsl:attribute name="for">
					<xsl:value-of select="@type"/>
				</xsl:attribute>
				<xsl:value-of select="@type"/>
			</label> <br />
		</xsl:for-each>
	</td>
</tr>
<tr>
	<th align="left"><label for="cur_password">Current Password:</label></th>
	<td align="left"><input type="password" name="cur_password" size="10" maxlength="70"/></td>
</tr>
<tr>
	<th align="left"><label for="new_password">New Password :</label>
		<br />(optional)</th>
	<td align="left">
		<input type="password" name="new_password" size="10" maxlength="70"/>
	</td>
</tr>
<tr>
	<th align="left"><label for="conf_new_password">Password Confirmation :</label></th>
	<td align="left">
		<input type="password" name="conf_new_password" size="10" maxlength="70"/>
	</td>
</tr>
</table>
<input type="hidden" name="sess_id">
	<xsl:attribute name="value">
		<xsl:value-of select="$session_id"/>
	</xsl:attribute>
</input>

<input type="submit" class="button" name="submit" value="Confirm"/>


</form>

</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>

</xsl:stylesheet>
