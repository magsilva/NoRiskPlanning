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

<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<div class="al_center">
<form action="groups_sch_ins_week_app.php" method="POST">
<table summary ="Appointment Insertion" class="data" cellspacing="5">
<tr>
	<th align="center"><label for="day">Day: </label></th>
	<th align="center"><label for="begtime">Beg. Time: </label></th>
	<th align="center"><label for="endtime">End. Time: </label></th>
	<th align="center"><label for="type">Type: </label></th>
</tr>
<tr>
	<td align="center">
		<xsl:variable name="cur_day" select="/nrp/schedule/appointment/appointment.dayofweek/@id"/>
		<select name="day">
			<xsl:for-each select="/nrp/dayofweek">
				<xsl:variable name="count" select="position()-1"/>
				<option>
				<xsl:attribute name="value">
					<xsl:copy-of select="$count"/>
				</xsl:attribute>
				<xsl:if test="$count = $cur_day">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="."/>
				</option>
			</xsl:for-each>
		</select>
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
				<xsl:variable name="type" select="type.name"/>
				<option>
				<xsl:attribute name="value">
					<xsl:copy-of select="$count"/>
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
	<th align="center" colspan="2"><label for="description">Description: </label></th>
	<th align="center" colspan="2"><label for="url">URL: </label></th>
</tr>
<tr>
	<td colspan="2" align="center">
		<input type="text" name="description" size="30" maxlength="100">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/schedule/appointment/appointment.description"/>
			</xsl:attribute>
		</input>
	</td>
	<td colspan="2" align="center">
		<input type="text" name="url" size="30" maxlength="100">
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

<input type="hidden" name="group_id">
	<xsl:attribute name="value">
		<xsl:value-of select="/nrp/schedule/appointment/appointment.group/@id"/>
	</xsl:attribute>
</input>

<input type="hidden" name="is_pop" value="1"/>


<br/><input type="submit" class="button" name="submit_ins" value="Insert"/> -
<input type="button" class="button" name="close" value="Cancel" onclick="javascript:window.close()"/>

</form>
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
