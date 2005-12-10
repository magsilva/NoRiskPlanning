<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="schedule">

<xsl:variable name="session_id" select="." />
<xsl:variable name="schedule_span" select="@span"/>

<xsl:if test="$schedule_span = 'day'">

</xsl:if>

<xsl:if test="$schedule_span = 'week'">

</xsl:if>

<xsl:if test="$schedule_span = 'month'">

</xsl:if>

<xsl:if test="$schedule_span = 'semester'">

</xsl:if>

</xsl:template>

</xsl:stylesheet>
