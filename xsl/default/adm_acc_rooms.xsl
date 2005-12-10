<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="adm_menu.xsl"/>

<xsl:include href="schedule.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />
<xsl:variable name="url_main" select="concat('adm_main.php?sess_id=', $session_id)" />
<xsl:variable name="accounts_url" select="concat('adm_accounts.php?sess_id=', $session_id)"/>
<xsl:variable name="new_room_url" select="concat('adm_acc_rooms_new.php?sess_id=', $session_id)"/>

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
	</xsl:attribute> Accounts
     </a> / Rooms
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
		<xsl:copy-of select="$new_room_url"/>
	</xsl:attribute>Create New Room
</a>
</p>

<xsl:if test="count(/nrp/room) = 0">
	<p class="big_head">
		There are no rooms.
	</p>
</xsl:if>

<xsl:if test="count(/nrp/room) &gt; 0">
<table width="90%" border="1">
<tr>
	<th class="sch_title">Account_id</th>
	<th class="sch_title">Name</th>
	<th class="sch_title">Code</th>
	<th class="sch_title">Type</th>
	<th class="sch_title">Capacity</th>
	<th class="sch_title">Modify</th>
	<th class="sch_title">Remove</th>
</tr>
<xsl:for-each select="/nrp/room">
<xsl:sort select="room.name"/>
<xsl:variable name="room_id" select="@id"/>
<xsl:variable name="modify_url" select="concat('adm_acc_rooms_modify.php?sess_id=', $session_id)"/>
<xsl:variable name="remove_url" select="concat('adm_acc_rooms_remove.php?sess_id=', $session_id)"/>
<tr>
	<td align="center"><xsl:value-of select="@id"/></td>
	<td align="center"><xsl:value-of select="room.name"/></td>
	<td align="center"><xsl:value-of select="room.code"/></td>
	<td align="center">
		<xsl:if test="room.type != ''">
			<xsl:value-of select="room.type"/>
		</xsl:if>
		<xsl:if test="room.type = ''">
			.
		</xsl:if>
	</td>
	<td align="center"><xsl:value-of select="room.capacity"/></td>
	<td align="center">
		<a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$modify_url"/>&amp;account_id=<xsl:value-of select="@id"/>
			</xsl:attribute>
			<img src="images/button_edit.png" border="0">
				<xsl:attribute name="alt">
					Modify Room <xsl:value-of select="@id"/>
				</xsl:attribute>
			</img>
		</a>
	</td>
	<td align="center">
		<a>
			<xsl:attribute name="href">
				<xsl:copy-of select="$remove_url"/>&amp;account_id=<xsl:value-of select="@id"/>
			</xsl:attribute>
			<img src="images/button_drop.png" border="0">
				<xsl:attribute name="alt">
					Remove Room <xsl:value-of select="@id"/>
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
