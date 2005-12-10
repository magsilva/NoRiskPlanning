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
<xsl:variable name="ins_app" select="concat('groups_sch_ins_app.php?sess_id=', $session_id)" />
<xsl:variable name="ins_week_app" select="concat('groups_sch_ins_week_app.php?sess_id=', $session_id)" />
<xsl:variable name="del_app" select="concat('groups_sch_del_app.php?sess_id=', $session_id)" />
<xsl:variable name="del_week_app" select="concat('groups_sch_del_week_app.php?sess_id=', $session_id)" />


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
<xsl:value-of select="/nrp/group/group.name"/></a> / Schedule Group Appointment
</div>

<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<p class="alert">
<xsl:value-of select="/nrp/group/group.name"/> - Weekly Schedule -
<xsl:value-of select="/nrp/schedule/span_start/span_start.month"/> /
<xsl:value-of select="/nrp/schedule/span_start/span_start.day"/> /
<xsl:value-of select="/nrp/schedule/span_start/span_start.year"/> to
<xsl:value-of select="/nrp/schedule/span_end/span_end.month"/> /
<xsl:value-of select="/nrp/schedule/span_end/span_end.day"/> /
<xsl:value-of select="/nrp/schedule/span_end/span_end.year"/>
</p>

<p class="options_center">
<a>
<xsl:attribute name="href">
	<xsl:value-of select="$ins_app"/>&amp;group_id=<xsl:value-of select="/nrp/group/@id"/>
</xsl:attribute>
Insert Appointment</a> -
<a>
<xsl:attribute name="href"><xsl:value-of select="$ins_week_app"/>&amp;group_id=<xsl:value-of select="/nrp/group/@id"/></xsl:attribute>
Insert Weekly Appointment</a><br/>
<a>
<xsl:attribute name="href"><xsl:value-of select="$del_app"/>&amp;group_id=<xsl:value-of select="/nrp/group/@id"/></xsl:attribute>
Remove Appointment</a> -
<a>
<xsl:attribute name="href"><xsl:value-of select="$del_week_app"/>&amp;group_id=<xsl:value-of select="/nrp/group/@id"/></xsl:attribute>
Remove Weekly Appointment</a>
</p>

<form method="POST" action="groups_schedule.php">
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
	<xsl:attribute name="value">
		<xsl:value-of select="/nrp/group/@id"/>
	</xsl:attribute>
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

<table border="1" width='95%'>
<xsl:attribute name="summary">
Weekly Schedule - <xsl:value-of select="/nrp/schedule/span_start/span_start.month"/> / <xsl:value-of select="/nrp/schedule/span_start/span_start.day"/> / <xsl:value-of select="/nrp/schedule/span_start/span_start.year"/> to <xsl:value-of select="/nrp/schedule/span_end/span_end.month"/> / <xsl:value-of select="/nrp/schedule/span_end/span_end.day"/> / <xsl:value-of select="/nrp/schedule/span_end/span_end.year"/>
</xsl:attribute>
<tr>
<th class="sch_title1">.</th>
<xsl:for-each select="/nrp/time">
	<th class="sch_title1">
		<xsl:value-of select="."/>
	</th>
</xsl:for-each>
</tr>

<xsl:for-each select="/nrp/date">
<xsl:variable name="cur_day" select="date.day"/>
<xsl:variable name="cur_month" select="date.month"/>
<xsl:variable name="cur_year" select="date.year"/>
<xsl:variable name="cur_dayofweek" select="date.dayofweek"/>
<tr>
<td class="sch_time" width="7%">
<xsl:value-of select="concat(concat(concat(concat(substring($cur_dayofweek, 1, 3), '-'), $cur_month), '/'), $cur_day)"/>
</td>
<xsl:for-each select="/nrp/schedule/appointment[appointment.date/appointment.date.day = $cur_day
	and appointment.date/appointment.date.month = $cur_month
	and appointment.date/appointment.date.year = $cur_year]">
	<xsl:if test="appointment.type != 'free'">
	<td class="appointment">
			<xsl:attribute name="colspan">
				<xsl:value-of select="appointment.length"/>
			</xsl:attribute>
			<xsl:attribute name="bgcolor">
				408383
			</xsl:attribute>
			Full
	</td>
	</xsl:if>
	<xsl:if test="appointment.type = 'free'">
		<td class="appointment">
		Free
		<a>
		<xsl:attribute name="href">
		javascript:show('groups_sch_ins_app.php?is_pop=1&amp;
		group_id=<xsl:value-of select="/nrp/group/@id"/>&amp;
		sess_id=<xsl:value-of select="$session_id"/>&amp;
		beg_time=<xsl:value-of select="appointment.beg_time/@id"/>&amp;
		end_time=<xsl:value-of select="appointment.end_time/@id"/>&amp;
		day=<xsl:value-of select="appointment.date/appointment.date.day"/>&amp;
		month=<xsl:value-of select="appointment.date/appointment.date.month"/>&amp;
		year=<xsl:value-of select="appointment.date/appointment.date.year"/>
		', 500, 400)
		</xsl:attribute>
		<img src="./images/button_new.png" border="0" height="8" width="7">
		<xsl:attribute name="alt">
		Schedule a new appointment at
		<xsl:value-of select="appointment.beg_time"/>
		</xsl:attribute>
		</img>
		</a>
		</td>
	</xsl:if>
</xsl:for-each>
</tr>
</xsl:for-each>
</table>

<br/>

</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
