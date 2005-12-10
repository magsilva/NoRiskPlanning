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
     Your location: NRP / <xsl:value-of select="/nrp/schedule/@id"/>Main
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
Monthly Schedule - <xsl:value-of select="/nrp/schedule/span_start/span_start.month/@name"/> -  <xsl:value-of select="/nrp/schedule/span_start/span_start.year"/> 
</p>

<form method="POST" action="main.php">
<p class="big_head">
<input type="hidden" name="span">
        <xsl:attribute name="value">
                <xsl:value-of select="/nrp/schedule/@span"/>
        </xsl:attribute>
</input>
<input class="button" type="submit" name="dec_span" alt="Previous week" value="&lt;&lt;"/> -
<input class="button" type="submit" name="current" value="This Month"/> -
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
<xsl:variable name="first_day_week" select="/nrp/first_day_week"/>

<table border="1" width='95%'>
<xsl:attribute name="summary">
Montly Schedule - <xsl:value-of select="/nrp/schedule/span_start/span_start.month"/> / <xsl:value-of select="/nrp/schedule/span_start/span_start.day"/> / <xsl:value-of select="/nrp/schedule/span_start/span_start.year"/> to <xsl:value-of select="/nrp/schedule/span_end/span_end.month"/> / <xsl:value-of select="/nrp/schedule/span_end/span_end.day"/> / <xsl:value-of select="/nrp/schedule/span_end/span_end.year"/>
</xsl:attribute>

<tr>
<xsl:for-each select="/nrp/day_of_week">
	<th class="sch_title"><xsl:value-of select="."/></th>
</xsl:for-each>
</tr>

<xsl:for-each select="/nrp/week">
<xsl:variable name="cur_week" select="."/>

<tr>
<xsl:if test="$cur_week = 0 and $first_day_week &gt; 0">
	<td>
		<xsl:attribute name="colspan">
			<xsl:value-of select="$first_day_week"/>
		</xsl:attribute>
	</td>
</xsl:if>
<xsl:for-each select="/nrp/date[@week = $cur_week]">
<td class="sch_month_day">
	<xsl:value-of select="date.day"/>

</td>
</xsl:for-each>
</tr>

<tr>
<xsl:if test="$cur_week = 0 and $first_day_week &gt; 0">
	<td>
		<xsl:attribute name="colspan">
			<xsl:value-of select="$first_day_week"/>
		</xsl:attribute>
	</td>
</xsl:if>

<xsl:for-each select="/nrp/date[@week = $cur_week]">
<xsl:variable name="cur_day" select="date.day"/>
<xsl:variable name="cur_month" select="date.month"/>
<xsl:variable name="cur_year" select="date.year"/>
<xsl:variable name="cur_dayofweek" select="date.dayofweek"/>

<td class="appointment_month">
<xsl:for-each select="/nrp/schedule/appointment[appointment.date/appointment.date.day = $cur_day
	and appointment.date/appointment.date.month = $cur_month
	and appointment.date/appointment.date.year = $cur_year]">
	<xsl:variable name="cur_icon" select="appointment.image"/>
	<xsl:value-of select="appointment.beg_time"/> -
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
	</xsl:if><br/>
</xsl:for-each>
<xsl:if test="count(/nrp/schedule/appointment[appointment.date/appointment.date.day = $cur_day
	and appointment.date/appointment.date.month = $cur_month
	and appointment.date/appointment.date.year = $cur_year]) = 0">.</xsl:if>
</td>
</xsl:for-each>
</tr>
</xsl:for-each>
</table>

<br/>

<p class="big_head">
Change visualization mode to:
</p>

<form method="POST" action="main.php">
<p class="big_head">
<input class="button" type="submit" name="span_day" value="Daily"/> -
<input class="button" type="submit" name="span_week" value="Weekly"/>
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
