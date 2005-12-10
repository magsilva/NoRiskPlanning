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
<xsl:variable name="schurl" select="concat('scheduling.php?sess_id=', $session_id)"/>

<div class="location">
     Your location: NRP / <xsl:value-of select="/nrp/schedule/@id"/> /
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$mainurl"/>
</xsl:attribute>
Main
</a>
/ <a>
<xsl:attribute name="href">
	<xsl:copy-of select="$schurl"/>
</xsl:attribute>Scheduling</a> / Modify Appointment
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<p class="big_head">
Fill in the form with the data you want to modify and press 'Modify':
</p>

<form action="sch_mod_app1.php" method="POST">
<table summary ="Appointment Insertion" class="data" cellspacing="5">
<tr>
	<th align="center"><label for="day">Day: </label></th>
	<th align="center"><label for="month">Month: </label></th>
	<th align="center"><label for="day">Year: </label></th>
	<th align="center"><label for="begtime">Beg. Time: </label></th>
	<th align="center"><label for="endtime">End. Time: </label></th>
	<th align="center"><label for="type">Type: </label></th>
</tr>
<tr>
	<td align="center">
		<input type="text" name="day" size="3" maxlength="2">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/schedule/appointment/appointment.date/appointment.date.day"/>
			</xsl:attribute>
		</input>
	</td>
	<td align="center">
		<xsl:variable name="cur_month" select="/nrp/schedule/appointment/appointment.date/appointment.date.month"/>
		<select name="month">
			<xsl:for-each select="/nrp/month">
				<xsl:variable name="count" select="position()"/>
				<option>
				<xsl:attribute name="value">
					<xsl:copy-of select="$count"/>
				</xsl:attribute>
				<xsl:if test="$count = $cur_month">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="."/>
				</option>
			</xsl:for-each>
		</select>
	</td>
	<td align="center">
		<input type="text" name="year" size="5" maxlength="4">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/schedule/appointment/appointment.date/appointment.date.year"/>
			</xsl:attribute>
		</input>
	</td>
	<td align="center">
		<xsl:variable name="cur_beg_time" select="/nrp/schedule/appointment/appointment.beg_time/@id"/>
		<select name="beg_time">
			<xsl:for-each select="/nrp/time">
				<xsl:variable name="count" select="position() - 1"/>
				<option>
				<xsl:attribute name="value">
					<xsl:copy-of select="$count"/>
				</xsl:attribute>
				<xsl:if test="$count = $cur_beg_time">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="."/>
				</option>
			</xsl:for-each>
		</select>
	</td>
	<td align="center">
		<xsl:variable name="cur_end_time" select="/nrp/schedule/appointment/appointment.end_time/@id"/>
		<select name="end_time">
			<xsl:for-each select="/nrp/time">
				<xsl:variable name="count" select="position() - 1"/>
				<option>
				<xsl:attribute name="value">
					<xsl:copy-of select="$count"/>
				</xsl:attribute>
				<xsl:if test="$count = $cur_end_time">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="."/>
				</option>
			</xsl:for-each>
		</select>
	</td>
	<td align="center">
		<xsl:variable name="cur_type" select="/nrp/schedule/appointment/appointment.type/@id"/>
		<select name="type">
			<xsl:for-each select="/nrp/type">
				<xsl:variable name="count" select="position() - 1"/>
				<option>
				<xsl:attribute name="value">
					<xsl:value-of select="$count"/>
				</xsl:attribute>
				<xsl:if test="$count = $cur_type">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="type.name"/>
				</option>
			</xsl:for-each>
		</select>
	</td>
</tr>
<tr></tr>
<tr></tr>
<tr>
	<th align="center" colspan="3"><label for="description">Description: </label></th>
	<th align="center" colspan="3"><label for="url">URL: </label></th>
</tr>
<tr>
	<td colspan="3" align="center">
		<input type="text" name="description" size="45" maxlength="100">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/schedule/appointment/appointment.description"/>
			</xsl:attribute>
		</input>
	</td>
	<td colspan="3" align="center">
		<input type="text" name="url" size="45" maxlength="100">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/schedule/appointment/appointment.url"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
</table>

<input type="hidden" name="sess_id">
	<xsl:attribute name="value">
		<xsl:value-of select="$session_id"/>
	</xsl:attribute>
</input>

<input type="hidden" name="app_id">
	<xsl:attribute name="value">
		<xsl:value-of select="/nrp/schedule/appointment/@id"/>
	</xsl:attribute>
</input>

<xsl:variable name="master_sess" select="/nrp/master_session"/>
<xsl:if test="$master_sess != 0">
	<xsl:variable name="ins_master" select="/nrp/ins_at_master"/>
	<input type="checkbox" name="ins_at_master">
		<xsl:if test="$ins_master = 1">
			<xsl:attribute name="checked">checked</xsl:attribute>
		</xsl:if>
	</input>
	<label for="ins_at_master">Modify this appointment at the Owner's Schedule</label>
</xsl:if>

<br/><input type="submit" class="button" name="submit_mod" value="Modify"/>

</form>

</div>


<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
