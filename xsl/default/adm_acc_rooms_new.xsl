<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="adm_menu.xsl"/>

<xsl:include href="schedule.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />
<xsl:variable name="url_main" select="concat('adm_main.php?sess_id=', $session_id)" />
<xsl:variable name="accounts_url" select="concat('adm_accounts.php?sess_id=', $session_id)"/>
<xsl:variable name="rooms_url" select="concat('adm_acc_rooms.php?sess_id=', $session_id)"/>

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
     Main</a> /
     <a>
	<xsl:attribute name="href">
		<xsl:copy-of select="$accounts_url"/>
	</xsl:attribute>
     Accounts</a> / 
     <a>
	<xsl:attribute name="href">
		<xsl:copy-of select="$rooms_url"/>
	</xsl:attribute>
     Rooms</a> / Create New Room
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

<form action="adm_acc_rooms_new.php" method="POST">
<table summary ="New Room information" class="data">
<tr>
	<th align="left"><label for="account_id">Account Id: </label></th>
	<td align="left"><input type="text" name="account_id" size="30" maxlength="32" >
		<xsl:attribute name="value">
			<xsl:value-of select="/nrp/room/@id"/>
		</xsl:attribute>
 	    </input>
	</td>
</tr>
<tr>
	<th align="left"><label for="name">Name: </label></th>
	<td align="left">
		<input type="text" name="name" size="40" maxlength="70">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/room/room.name"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="code">Code: </label></th>
	<td align="left">
		<input type="text" name="code" size="16" maxlength="16">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/room/room.code"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="type">Type: </label></th>
	<td align="left">
		<input type="text" name="type" size="20" maxlength="30">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/room/room.type"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="capacity">Capacity: </label></th>
	<td align="left">
		<input type="text" name="capacity" size="8" maxlength="8">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/room/room.capacity"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="location">Location: </label><br/> (optional)</th>
	<td align="left">
		<textarea name="location" rows="3" cols="40">
			<xsl:value-of select="/nrp/room/room.location"/>
		</textarea>
	</td>
</tr>
<tr>
	<th align="left"><label for="comments">Comments: </label><br/> (optional)</th>
	<td align="left">
		<textarea name="comments" rows="3" cols="40">
			<xsl:value-of select="/nrp/room/room.comments"/>
		</textarea>
	</td>
</tr>
</table>
<input type="hidden" name="sess_id">
	<xsl:attribute name="value">
		<xsl:value-of select="$session_id"/>
	</xsl:attribute>
</input>

<br/><input type="submit" class="button" name="create" value="Create New Room"/>

</form>

<br/>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
