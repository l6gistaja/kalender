<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<xsl:template match="/">

<xsl:for-each select="kalender/db/urls/r"><xsl:variable name="ucid" select="@urlcategory_id"/><xsl:if test="$ucid != 1 and $ucid != 7">wget -O 'web/<xsl:value-of select="@id"/>.html' '<xsl:value-of select="/kalender/db/urlcategories/r[@id=$ucid]/urlprefix"/><xsl:value-of select="url"/>';
</xsl:if></xsl:for-each>

</xsl:template>
</xsl:stylesheet>