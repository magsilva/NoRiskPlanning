<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="menu.xsl"/>

<xsl:include href="schedule.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />

<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US"
xml:lang="en-US">

<head>
	<title>No Risk Planning</title>
	<link rel="stylesheet" type="text/css" href="css/nrp.css" />

<script language="JavaScript">
function show( hp, wdt, hgt ) {	properties = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=' + wdt + ',height=' + hgt;	window.open( hp, 'Info', properties );}
</script>

</head>

<body>

<xsl:apply-templates select = "nrp/sess_id"/>
<xsl:variable name="mainurl" select="concat('main.php?sess_id=', $session_id)" />
<xsl:variable name="groupsurl" select="concat('groups.php?sess_id=', $session_id)" />
<xsl:variable name="cur_group" select="concat('groups_enter.php?sess_id=', $session_id)" />

<div class="location">
     Your location: NRP /
<a>
<xsl:attribute name="href">
	<xsl:copy-of select="$mainurl"/>
</xsl:attribute>
Main
</a>
/ <a>
<xsl:attribute name="href">
	<xsl:value-of select="$groupsurl"/>
</xsl:attribute>
Groups</a> /
<a>
<xsl:attribute name="href">
	<xsl:value-of select="$cur_group"/>&amp;group_id=<xsl:value-of select="/nrp/group/@id"/>
</xsl:attribute>
<xsl:value-of select="/nrp/group/group.name"/></a> / 
Group Schedule
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<p class="big_title">
Weekly Schedule - <xsl:value-of select="/nrp/schedule/span_start/span_start.month"/> / <xsl:value-of select="/nrp/schedule/span_start/span_start.day"/> / <xsl:value-of select="/nrp/schedule/span_start/span_start.year"/> to <xsl:value-of select="/nrp/schedule/span_end/span_end.month"/> / <xsl:value-of select="/nrp/schedule/span_end/span_end.day"/> / <xsl:value-of select="/nrp/schedule/span_end/span_end.year"/>
</p>

<form method="POST" action="groups_members_schedule.php">
<p class="big_head">
<input type="hidden" name="span">
        <xsl:attribute name="value">
                <xsl:value-of select="/nrp/schedule/@span"/>
        </xsl:attribute>
</input>
<input class="button" type="submit" name="dec_span" alt="Previous week" value="&lt;&lt;"/> -
<input class="button" type="submit" name="current" value="This Week"/> -
<input class="button" type="submit" name="inc_span" value="&gt;&gt;"/>
<input type="hidden" name="sess_id">
        <xsl:attribute name="value">
                <xsl:value-of select="$session_id"/>
        </xsl:attribute>
</input>
<input type="hidden" name="group_id">
	<xsl:attribute name="value"><xsl:value-of select="/nrp/group/@id"/></xsl:attribute>
</input>
<input type="hidden" name="span_start_day">
        <xsl:attribute name="value">
                <xsl:value-of select="/nrp/schedule/span_start/span_start.day"/>
        </xsl:attribute>
</input>
<input type="hidden" name="span_start_month">
        <xsl:attribute name="value">
                <xsl:value-of select="/nrp/schedule/span_start/span_start.month"/>
        </xsl:attribute>
</input>
<input type="hidden" name="span_start_year">
        <xsl:attribute name="value">
                <xsl:value-of select="/nrp/schedule/span_start/span_start.year"/>
        </xsl:attribute>
</input>
<input type="hidden" name="span_end_day">
        <xsl:attribute name="value">
                <xsl:value-of select="/nrp/schedule/span_end/span_end.day"/>
        </xsl:attribute>
</input>
<input type="hidden" name="span_end_month">
        <xsl:attribute name="value">
                <xsl:value-of select="/nrp/schedule/span_end/span_end.month"/>
        </xsl:attribute>
</input>
<input type="hidden" name="span_end_year">
        <xsl:attribute name="value">
                <xsl:value-of select="/nrp/schedule/span_end/span_end.year"/>
        </xsl:attribute>
</input>
</p>
</form>

<xsl:variable name="num_time" select="count(/nrp/time) -1"/>
<xsl:variable name="num_time1" select="count(/nrp/time)"/>

<xsl:for-each select="/nrp/date">
<xsl:variable name="cur_day" select="date.day"/>
<xsl:variable name="cur_month" select="date.month"/>
<xsl:variable name="cur_year" select="date.year"/>
<xsl:variable name="cur_dayofweek" select="date.dayofweek"/>

<table border="1" width='95%'>
<xsl:attribute name="summary">Group Schedule - <xsl:value-of select="$cur_dayofweek"/>
</xsl:attribute>
<tr>
<th class="sch_title1"><xsl:value-of select="concat(concat(concat(concat(substring($cur_dayofweek, 1, 3), '-'), $cur_month), '/'), $cur_day)"/></th>
<xsl:for-each select="/nrp/time">
	<th class="sch_title1">
		<xsl:value-of select="."/>
	</th>
</xsl:for-each>
</tr>

<xsl:for-each select="/nrp/group/group.member">
<xsl:variable name="member" select="@id"/>
<tr>
<td class="sch_time" width="7%">
	<xsl:value-of select="group.member.name"/>
</td>

<xsl:for-each select="/nrp/schedule[@id=$member]/appointment[appointment.date/appointment.date.day = $cur_day
	and appointment.date/appointment.date.month = $cur_month
	and appointment.date/appointment.date.year = $cur_year]">
	<xsl:variable name="cur_icon" select="appointment.image"/>
			<xsl:if test="appointment.before &gt; 0">
				<td>
				<xsl:attribute name="colspan">
					<xsl:value-of select="appointment.before"/>
				</xsl:attribute>
				</td>
			</xsl:if>
	<td class="appointment">
			<xsl:attribute name="colspan">
				<xsl:value-of select="appointment.length"/>
			</xsl:attribute>
			<xsl:attribute name="bgcolor">
			<xsl:value-of select="appointment.color"/>
			</xsl:attribute>

			<xsl:if test="$cur_icon != ''">
			<img>
			<xsl:attribute name="src">
				<xsl:value-of select="$cur_icon"/>
			</xsl:attribute>
			</img>
			</xsl:if>
			<xsl:if test="$cur_icon = ''">
				(<xsl:value-of select="substring(appointment.type, 1, 1)"/>)
			</xsl:if>

			<xsl:if test="appointment.url = ''">
			<xsl:value-of select="appointment.description"/>
			</xsl:if>
			<xsl:if test="appointment.url != ''">
			<a target="_blank">
			<xsl:attribute name="href">
			<xsl:value-of select="appointment.url"/>
			</xsl:attribute>
			<xsl:value-of select="appointment.description"/>
			</a>
			</xsl:if>
			</td>
			<xsl:if test="appointment.after &gt; 0">
				<td>
				<xsl:attribute name="colspan">
					<xsl:value-of select="appointment.after"/>
				</xsl:attribute>
				</td>
			</xsl:if>
</xsl:for-each>
</tr>
</xsl:for-each>

</table><br />
</xsl:for-each>

<table border="1" width='95%' summary="Appointments Legend">
<tr>
<xsl:for-each select="/nrp/app_type">
<xsl:variable name="cur_icon" select="app_type.icon"/>
	<th class="al_center" width="5%">
		<xsl:attribute name="bgcolor">
			<xsl:value-of select="app_type.color"/>
		</xsl:attribute>
		<xsl:if test="$cur_icon != ''">
		<img>
			<xsl:attribute name="src">
				<xsl:value-of select="app_type.icon"/>
			</xsl:attribute>
		</img>
		</xsl:if>
		<xsl:if test="$cur_icon = ''">
			<xsl:value-of select="substring(app_type.name, 1, 1)"/>
		</xsl:if>
	</th>
	<td>
		<xsl:value-of select="app_type.name"/>
	</td>
</xsl:for-each>
</tr>
</table>
<br/>

</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
