<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="adm_menu.xsl"/>

<xsl:include href="schedule.xsl"/>

<xsl:template match="/">

<xsl:variable name="session_id" select="/nrp/sess_id" />
<xsl:variable name="url_main" select="concat('adm_main.php?sess_id=', $session_id)" />
<xsl:variable name="accounts_url" select="concat('adm_accounts.php?sess_id=', $session_id)"/>
<xsl:variable name="people_url" select="concat('adm_acc_people.php?sess_id=', $session_id)"/>

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
		<xsl:copy-of select="$people_url"/>
	</xsl:attribute>
     People</a> / Modify Person
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

<form action="adm_acc_people_modify.php" method="POST">
<table summary ="Person information" class="data">
<tr>
	<th align="left"><label for="account_id">Account Id: </label></th>
	<td align="left"><input type="hidden" name="account_id" size="30" maxlength="32" >
		<xsl:attribute name="value">
			<xsl:value-of select="/nrp/person/@id"/>
		</xsl:attribute>
 	    </input><xsl:value-of select="/nrp/person/@id"/>
	</td>
</tr>
<tr>
	<th align="left"><label for="name">Name: </label></th>
	<td align="left">
		<input type="text" name="name" size="40" maxlength="70">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/person/person.name"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="name">E-mail: </label></th>
	<td align="left">
		<input type="text" name="email" size="40" maxlength="100">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/person/person.email"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="name">URL: </label><br/> (optional)</th>
	<td align="left">
		<input type="text" name="url" size="40" maxlength="100">
			<xsl:attribute name="value">
				<xsl:value-of select="/nrp/person/person.url"/>
			</xsl:attribute>
		</input>
	</td>
</tr>
<tr>
	<th align="left"><label for="dep_id">Department: </label></th>
	<td align="left">
	<select name="dep_id">
	<xsl:variable name="cur_dep_id" select="/nrp/person/person.department/@id"/>
		<xsl:for-each select="/nrp/department">
		<xsl:sort select="department.name"/>
			<option>
				<xsl:attribute name="value">
					<xsl:value-of select="@id"/>
				</xsl:attribute>
				<xsl:if test="@id = $cur_dep_id">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="department.name"/>
			</option>
		</xsl:for-each>
	</select>
	</td>
</tr>
<tr>
	<th align="left"><label for="category">Category: </label></th>
	<td align="left">
	<select name="category">
	<xsl:variable name="cur_category" select="/nrp/person/person.category/@id"/>
		<xsl:for-each select="/nrp/category">
		<xsl:sort select="category.name"/>
			<option>
				<xsl:attribute name="value">
					<xsl:value-of select="@id"/>
				</xsl:attribute>
				<xsl:if test="@id = $cur_category">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="category.name"/>
			</option>
		</xsl:for-each>
	</select>
	</td>
</tr>
<tr>
	<th align="left"><label for="comments">Comments: </label><br/> (optional)</th>
	<td align="left">
		<textarea name="comments" rows="3" cols="40">
			<xsl:value-of select="/nrp/person/person.comments"/>
		</textarea>
	</td>
</tr>
</table>
<input type="hidden" name="sess_id">
	<xsl:attribute name="value">
		<xsl:value-of select="$session_id"/>
	</xsl:attribute>
</input>

<br/><input type="submit" class="button" name="modify" value="Modify Person"/>

</form>

<br/>
</div>

<div class="down_screen">
</div>

</body>
</html>

</xsl:template>
</xsl:stylesheet>
