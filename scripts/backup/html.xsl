<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:variable name="dir_root">../../../</xsl:variable>

<xsl:variable name="theader">
    <tr>
      <th>ID</th>
      <th>eID</th>
      <th>Tähtpäev</th>
      <th>Maausk</th>
      <th>Sait</th>
      <th>URL</th>
    </tr>
</xsl:variable>

<xsl:template match="/">
  <html>
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <title>Backup <xsl:value-of select="/kalender/timeiso8601"/></title>
  <link rel="StyleSheet" type="text/css" media="screen"><xsl:attribute name="href"><xsl:copy-of select="$dir_root"/>kalender.css</xsl:attribute></link>
  <link rel="SHORTCUT ICON"><xsl:attribute name="href"><xsl:copy-of select="$dir_root"/>favicon.ico</xsl:attribute></link>
  </head>
  <body>
  <h1>Backup <xsl:value-of select="/kalender/timeiso8601"/></h1>
  <table>

    <xsl:copy-of select="$theader"/>

      <xsl:for-each select="kalender/db/urls/r">
      <xsl:variable name="ucid" select="@urlcategory_id"/>
      <xsl:if test="$ucid != 1 and $ucid != 7">
      
	<xsl:variable name="eid" select="@event_id"/>
	<xsl:variable name="url"><xsl:value-of select="/kalender/db/urlcategories/r[@id=$ucid]/urlprefix"/><xsl:value-of select="url"/></xsl:variable>
	<tr>
	  <td valign="top"><a><xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute><a><xsl:attribute name="href">web/<xsl:value-of select="@id"/>.html</xsl:attribute><xsl:value-of select="@id"/></a></a></td>
	  <td valign="top"><a><xsl:attribute name="href"><xsl:copy-of select="$dir_root"/>tahtpaevad.html#<xsl:value-of select="@event_id"/></xsl:attribute><xsl:value-of select="@event_id"/></a></td>
	  <td valign="top"><xsl:value-of select="/kalender/db/events/r[@id=$eid]/event"/></td>
	  <td valign="top"><xsl:value-of select="/kalender/db/events/r[@id=$eid]/maausk"/></td>
	  <td valign="top"><a><xsl:attribute name="href"><xsl:value-of select="/kalender/db/urlcategories/r[@id=$ucid]/site"/></xsl:attribute><xsl:value-of select="/kalender/db/urlcategories/r[@id=$ucid]/urlcategory"/></a></td>
	  <td valign="top"><a><xsl:attribute name="href"><xsl:value-of select="$url"/></xsl:attribute><xsl:value-of select="$url"/></a></td>
	</tr>
	
      </xsl:if>
    
    </xsl:for-each>
    
    <xsl:copy-of select="$theader"/>
    
  </table>
  </body>
  </html>
</xsl:template>

</xsl:stylesheet>