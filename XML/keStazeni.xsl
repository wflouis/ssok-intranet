<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="/">
		<html lang="cs">
		    <title></title>
			<style type="text/css">
				h2,h3,h4 { text-indent: 50px;}
				ul { margin-left: 100px;}
			</style>
			<body>
				<xsl:for-each select="ke_stazeni">
					<xsl:for-each select="soubor">
						<ul>
							<li><a href="{@href}" ><xsl:value-of select = "@text" /></a></li>
						</ul>
					</xsl:for-each>
					<xsl:for-each select="slozka">
						<h4><xsl:value-of select = "@text" /></h4>
						<xsl:for-each select="soubor">
							<ul>
								<li><a href="{@href}" ><xsl:value-of select = "@text" /></a></li>
							</ul>
						</xsl:for-each>
					</xsl:for-each>
					<xsl:for-each select="hlavni_slozka">
						<h2><xsl:value-of select = "@text" /></h2>
						<xsl:for-each select="soubor">
							<ul>
								<li><a href="{@href}" ><xsl:value-of select = "@text" /></a></li>
							</ul>
						</xsl:for-each>
						<xsl:for-each select="slozka">
							<h4><xsl:value-of select = "@text" /></h4>
							<xsl:for-each select="soubor">
								<ul>
									<li><a href="{@href}" ><xsl:value-of select = "@text" /></a></li>
								</ul>
							</xsl:for-each>
						</xsl:for-each>
					</xsl:for-each>
				</xsl:for-each>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>