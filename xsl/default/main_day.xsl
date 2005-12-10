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
Daily Schedule - <xsl:value-of select="/nrp/schedule/span_start/span_start.month"/>/<xsl:value-of select="/nrp/schedule/span_start/span_start.day"/>/<xsl:value-of select="/nrp/schedule/span_start/span_start.year"/>
</p>

<form method="POST" action="main.php">
<p class="big_head">
<input type="hidden" name="span">
        <xsl:attribute name="value">
                <xsl:value-of select="/nrp/schedule/@span"/>
        </xsl:attribute>
</input>
<input class="button" type="submit" name="dec_span" alt="Previous day" value="&lt;&lt;"/> -
<input class="button" type="submit" name="current" value="Today"/> -
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

<table width="100%">
<tr><td align="right" width="80%">
<table border="1" width='80%'>
<xsl:attribute name="summary">
	Daily Schedule - <xsl:value-of select="/nrp/schedule/span_start/span_start.month"/> / <xsl:value-of select="/nrp/schedule/span_start/span_start.day"/> / <xsl:value-of select="/nrp/schedule/span_start/span_start.year"/>
</xsl:attribute>
<tr>
<th class="sch_title" width="2%">Time</th>
<th class="sch_title" colspan="3">Description</th>

</tr>
<xsl:for-each select="/nrp/time">
<xsl:variable name="cur_time" select="."/>
	<tr><td class="sch_time"><xsl:copy-of select="$cur_time"/></td>
	<xsl:if test="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.periodicity != 'fake'">
		<td>
			<xsl:if test="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.group != '' and /nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.group/@id > 0">
				<xsl:attribute name="colspan">3</xsl:attribute>
			</xsl:if>
			<xsl:attribute name="rowspan">
			<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.length"/>
			</xsl:attribute>
			<xsl:attribute name="bgcolor">
			<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.color"/>
			</xsl:attribute>
			<xsl:if test="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.url = ''">
			<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.description"/>
			</xsl:if>
			<xsl:if test="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.url != ''">
			<a target="_blank">
			<xsl:attribute name="href">
			<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.url"/>
			</xsl:attribute>
			<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.description"/>
			</a>
			</xsl:if>
			(<xsl:value-of select="substring(/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.type, 1, 1)"/>)
		</td>
		<xsl:if test="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.group = '' or /nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.group/@id = 0">
		<td class="al_center" width="5%">
			<xsl:attribute name="rowspan">
			<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.length"/>
			</xsl:attribute>
		<a>
			<xsl:attribute name="href">
			javascript:show('
			<xsl:if test="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.periodicity = 'common'">
			sch_mod_app1.php?is_pop=1&amp;
			sess_id=<xsl:value-of select="$session_id"/>&amp;app_id=
			</xsl:if>
			<xsl:if test="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.periodicity = 'weekly'">
			sch_mod_week_app1.php?is_pop=1&amp;
			sess_id=<xsl:value-of select="$session_id"/>&amp;
			date_day=<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.date/appointment.date.day"/>&amp;
			date_month=<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.date/appointment.date.month"/>&amp;
			date_year<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.date/appointment.date.year"/>&amp;app_id=
			</xsl:if>
			<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/@id"/>
			', 500, 400)
			</xsl:attribute>
			<img src="images/button_edit.png" border="0">
			<xsl:attribute name="alt">
			Modify Appointment <xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.description"/>
			</xsl:attribute>
			</img>
		</a>
		</td>
		<td class="al_center" width="5%">
			<xsl:attribute name="rowspan">
			<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.length"/>
			</xsl:attribute>
		<a>
			<xsl:attribute name="href">
			javascript:show('
			<xsl:if test="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.periodicity = 'common'">
			sch_del_app.php?is_pop=1&amp;
			sess_id=<xsl:value-of select="$session_id"/>&amp;app_id=
			</xsl:if>
			<xsl:if test="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.periodicity = 'weekly'">
			sch_del_week_app.php?is_pop=1&amp;
			sess_id=<xsl:value-of select="$session_id"/>&amp;app_id=
			</xsl:if>
			<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/@id"/>
			', 500, 400)
			</xsl:attribute>
			<img src="images/button_drop.png" border="0">
			<xsl:attribute name="alt">
			Remove Appointment <xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.description"/>
			</xsl:attribute>
			</img>
		</a>
		</td>
		</xsl:if>
	</xsl:if>
	<xsl:if test="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.periodicity = 'fake'">
	<td colspan="3">
		<a>
		<xsl:attribute name="href">
		javascript:show('sch_ins_app.php?is_pop=1&amp;
		sess_id=<xsl:value-of select="$session_id"/>&amp;
		beg_time=<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.beg_time/@id"/>&amp;
		end_time=<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.end_time/@id"/>&amp;
		day=<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.date/appointment.date.day"/>&amp;
		month=<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.date/appointment.date.month"/>&amp;
		year=<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.date/appointment.date.year"/>
		', 500, 400)
		</xsl:attribute>
		<img src="./images/button_new.png" border="0">
		<xsl:attribute name="alt">
		Schedule a new appointment at
		<xsl:value-of select="/nrp/schedule/appointment[appointment.beg_time = $cur_time]/appointment.beg_time"/>
		</xsl:attribute>
		</img>
		</a>
	</td>
	</xsl:if>
	</tr>
</xsl:for-each>
</table></td>
<td align="center" width="20%" valign="top">

<p class="big_title">Legend</p>

<table border="1" width='60%' summary="Appointments Legend">
<tr>
<th class="sch_title" width="15%">
Color
</th>
<th class="sch_title" width="85%">
Type
</th>
</tr>
<xsl:for-each select="/nrp/app_type">
<xsl:variable name="cur_icon" select="app_type.icon"/>
<tr>
	<th>
		<xsl:attribute name="bgcolor">
			<xsl:value-of select="app_type.color"/>
		</xsl:attribute>
                        <xsl:if test="$cur_icon != ''">
                        <img>
                        <xsl:attribute name="src">
                                <xsl:value-of select="$cur_icon"/>
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
                        (<xsl:value-of select="substring(app_type.name, 1, 1)"/>)
                        </xsl:if>
	</th>
	<td>
		<xsl:value-of select="app_type.name"/>
	</td>
</tr>
</xsl:for-each>
</table>
</td>
</tr>
</table>
<br/>

<p class="big_head">
Change visualization mode to:
</p>

<form method="POST" action="main.php">
<p class="big_head">
<input class="button" type="submit" name="span_week" value="Weekly"/> -
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
