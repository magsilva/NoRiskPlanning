<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="adm_menu.xsl"/>

<xsl:include href="schedule.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />
<xsl:variable name="url_main" select="concat('adm_main.php?sess_id=', $session_id)" />
<xsl:variable name="accounts_url" select="concat('adm_accounts.php?sess_id=', $session_id)"/>
<xsl:variable name="courses_url" select="concat('adm_acc_courses.php?sess_id=', $session_id)"/>

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
		<xsl:copy-of select="$courses_url"/>
	</xsl:attribute>
     Courses</a> / Modify Course
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

<form action="adm_acc_courses_modify.php" method="POST">
<table summary ="Course information" class="data">
<tr>
	<th align="left"><label for="account_id">Account Id: </label></th>
	<td align="left"><input type="hidden" name="account_id">
		<xsl:attribute name="value">
			<xsl:value-of select="/nrp/course/@id"/>
		</xsl:attribute>
 	    </input><xsl:value-of select="/nrp/course/@id"/>
	</td>
</tr>
<tr>
	<th align="left"><label for="name">Name: </label></th>
	<td align="left">
		<input type="text" name="name" size="40" maxlength="70">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/course/course.name"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="code">Code: </label></th>
	<td align="left">
		<input type="text" name="code" size="16" maxlength="16">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/course/course.code"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="group">Group: </label></th>
	<td align="left">
		<input type="text" name="group" size="16" maxlength="16">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/course/course.group"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="year">Year: </label></th>
	<td align="left">
		<input type="text" name="year" size="5" maxlength="4">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/course/course.year"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="semester">Semester: </label></th>
	<td align="left">
		<input type="text" name="semester" size="5" maxlength="4">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/course/course.semester"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="lecturer">Lecturer: </label></th>
	<td align="left">
		<select name="lecturer">
		<option value="">
		<xsl:if test="/nrp/course/course.lecturer = ''">
			<xsl:attribute name="selected">selected</xsl:attribute>
		</xsl:if>Choose a lecturer
		</option>
		<xsl:for-each select="/nrp/person">
		<xsl:variable name="cur_lecturer" select="/nrp/course/course.lecturer/@id"/>
			<option>
				<xsl:attribute name="value">
					<xsl:value-of select="@id"/>
				</xsl:attribute>
				<xsl:if test="@id = $cur_lecturer">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
			<xsl:value-of select="person.name"/>
			</option>
		</xsl:for-each>
		</select>
	</td>
</tr>

<tr>
	<th align="left"><label for="comments">Comments: </label><br/> (optional)</th>
	<td align="left">
		<textarea name="comments" rows="3" cols="40">
			<xsl:value-of select="/nrp/course/course.comments"/>
		</textarea>
	</td>
</tr>
</table>
<input type="hidden" name="sess_id">
	<xsl:attribute name="value">
		<xsl:value-of select="$session_id"/>
	</xsl:attribute>
</input>

<br/><input type="submit" class="button" name="modify" value="Modify Course"/>

</form>

<br/>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
