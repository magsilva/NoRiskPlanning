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

<script language="JavaScript">
function show( hp, wdt, hgt ) {	properties = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=' + wdt + ',height=' + hgt;	window.open( hp, 'Info', properties );}
</script>

</head>

<body>

<xsl:if test="/nrp/master_session = 0">
	<xsl:apply-templates select = "/nrp/sess_id"/>
</xsl:if>
<xsl:if test="/nrp/master_session != 0">
	<xsl:apply-templates select = "/nrp/master_session"/>
</xsl:if>

<div class="location">
     Your location: NRP / <xsl:value-of select="/nrp/schedule/@id"/> / Main
</div>


<div class="mid_screen">
<br />
<xsl:for-each select="/nrp/error">
	<p class="error">Error: <xsl:value-of select="."/></p>
</xsl:for-each>
<xsl:for-each select="/nrp/alert">
	<p class="alert"><xsl:value-of select="."/></p>
</xsl:for-each>

<xsl:if test="/nrp/unauthorized_appointments &gt; 0">
	<p class="big_head">You have <a>
	<xsl:attribute name="href">
		sch_auth.php?sess_id=<xsl:value-of select="$session_id"/>
	</xsl:attribute>unauthorized appointments</a> in your schedule.
	</p>
</xsl:if>

<p class="big_title">
Weekly Schedule - <xsl:value-of select="/nrp/schedule/span_start/span_start.month"/>/<xsl:value-of select="/nrp/schedule/span_start/span_start.day"/>/<xsl:value-of select="/nrp/schedule/span_start/span_start.year"/> to <xsl:value-of select="/nrp/schedule/span_end/span_end.month"/>/<xsl:value-of select="/nrp/schedule/span_end/span_end.day"/>/<xsl:value-of select="/nrp/schedule/span_end/span_end.year"/>
</p>

<p class="small_head">To schedule appointments, click on the blank cells of the table.</p>

<form method="POST" action="main.php">
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
	<xsl:if test="appointment.periodicity != 'fake'">
	<xsl:variable name="cur_icon" select="appointment.image"/>
	<td class="appointment">
			<xsl:attribute name="colspan">
				<xsl:value-of select="appointment.length"/>
			</xsl:attribute>
			<xsl:attribute name="bgcolor">
			<xsl:value-of select="appointment.color"/>
			</xsl:attribute>

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

			.<xsl:if test="$cur_icon != ''">
			<img>
			<xsl:attribute name="src">
				<xsl:value-of select="$cur_icon"/>
			</xsl:attribute>
			<xsl:attribute name="alt">
				<xsl:value-of select="appointment.type"/>
			</xsl:attribute>
			<xsl:attribute name="title">
				<xsl:value-of select="appointment.type"/>
			</xsl:attribute>
			</img>
			</xsl:if>
			<xsl:if test="$cur_icon = ''">
			(<xsl:value-of select="substring(appointment.type, 1, 1)"/>)
			</xsl:if>

		<xsl:if test="appointment.group = '' or appointment.group/@id = 0">
			.<a>
			<xsl:attribute name="href">javascript:show('
			<xsl:if test="appointment.periodicity = 'common'">
			sch_mod_app1.php?is_pop=1&amp;
			sess_id=<xsl:value-of select="$session_id"/>&amp;app_id=
			</xsl:if>
			<xsl:if test="appointment.periodicity = 'weekly'">
			sch_mod_week_app1.php?is_pop=1&amp;
			sess_id=<xsl:value-of select="$session_id"/>&amp;
			date_day=<xsl:value-of select="appointment.date/appointment.date.day"/>&amp;
			date_month=<xsl:value-of select="appointment.date/appointment.date.month"/>&amp;
			date_year=<xsl:value-of select="appointment.date/appointment.date.year"/>&amp;app_id=
			</xsl:if>
			<xsl:value-of select="@id"/>', 500, 400)
			</xsl:attribute>
			<img src="images/button_edit.png" border="0">
			<xsl:attribute name="alt">
			Modify Appointment '<xsl:value-of select="appointment.description"/>'
			</xsl:attribute>
			<xsl:attribute name="title">
			Modify Appointment '<xsl:value-of select="appointment.description"/>'
			</xsl:attribute>
			</img>
		</a>. <a>
			<xsl:attribute name="href">javascript:show('
			<xsl:if test="appointment.periodicity = 'common'">
			sch_del_app.php?is_pop=1&amp;
			sess_id=<xsl:value-of select="$session_id"/>&amp;app_id=
			</xsl:if>
			<xsl:if test="appointment.periodicity = 'weekly'">
			sch_del_week_app.php?is_pop=1&amp;
			sess_id=<xsl:value-of select="$session_id"/>&amp;app_id=
			</xsl:if>
			<xsl:value-of select="@id"/>', 500, 400)
			</xsl:attribute>
			<img src="images/button_drop.png" border="0">
			<xsl:attribute name="alt">
			Remove Appointment '<xsl:value-of select="appointment.description"/>'
			</xsl:attribute>
			<xsl:attribute name="title">
			Remove Appointment '<xsl:value-of select="appointment.description"/>'
			</xsl:attribute>
			</img>
		</a>
		</xsl:if>
		</td>
	</xsl:if>
	<xsl:if test="appointment.periodicity = 'fake'">
		<td class="appointment">
		<a>
		<xsl:attribute name="href">
		javascript:show('sch_ins_app.php?is_pop=1&amp;
		sess_id=<xsl:value-of select="$session_id"/>&amp;
		beg_time=<xsl:value-of select="appointment.beg_time/@id"/>&amp;
		end_time=<xsl:value-of select="appointment.end_time/@id"/>&amp;
		day=<xsl:value-of select="appointment.date/appointment.date.day"/>&amp;
		month=<xsl:value-of select="appointment.date/appointment.date.month"/>&amp;
		year=<xsl:value-of select="appointment.date/appointment.date.year"/>
		', 500, 400)
		</xsl:attribute>
		<img src="./images/button_new.png" border="0">
		<xsl:attribute name="alt">Schedule a new appointment at <xsl:value-of select="appointment.beg_time"/></xsl:attribute>
		<xsl:attribute name="title">Schedule a new appointment at <xsl:value-of select="appointment.beg_time"/></xsl:attribute>
		</img>
		</a>
		</td>
	</xsl:if>
</xsl:for-each>
</tr>
</xsl:for-each>
</table>

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
			<xsl:attribute name="alt">
				<xsl:value-of select="app_type.name"/>
			</xsl:attribute>
			<xsl:attribute name="title">
				<xsl:value-of select="app_type.name"/>
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

<p class="big_head">
Change visualization mode to:
</p>

<form method="POST" action="main.php">
<p class="big_head">
<input class="button" type="submit" name="span_day" value="Daily"/> -
<input class="button" type="submit" name="span_month" value="Monthly"/>
<input type="hidden" name="sess_id">
        <xsl:attribute name="value">
                <xsl:value-of select="$session_id"/>
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

</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
