<?php

include("hl7_vinfo.php");
include("hl7_header.php");
include("hl7_buttons.php");
include("hl7_footer.php");
include("db.php");

$ghl7_version = "";
$header1 = "HL7 Comprehensive Database ";
$header2 = "Contents";
$header3 = "";

$nButtonParameter = 0;
$sButtonParameter = "";
$sButtonType = "version";

PrintHeader();

$SQL = "SELECT * from HL7Versions where display=true order by hl7_version desc";

$bEven = FALSE;

$result = odbc_exec($linkID, $SQL);

$Fields = odbc_num_fields($result); 
print "<h2 align=\"center\">Contents</h2>\n"; 
print "<p>\n"; 
print "<table border='0' width='100%'>\n"; 

// Table Body 
$Outer=0; 
while( odbc_fetch_row( $result ))
{ 
	$Outer++; 
	if ($bEven == FALSE)
	{
		$bEven = TRUE;
		print "<tr class=\"info-even\">\n"; 
	}
	else
	{
		$bEven = FALSE;
		print "<tr class=\"info-odd\">\n"; 
	}
//	printf("<td valign=\"center\" align=center width=\"15%%\"><a href=\"hl7_version.php?vVersion=%s\"><img src=\"images/%s.gif\" alt=\"%s\" border=\"0\"></a></td>\n", 
	printf("<td valign=\"center\" align=center width=\"15%%\"><a href=\"hl7_version.php?vVersion=%s\"><div class=\"button\">%s</div></a></td>\n", 
				odbc_result( $result, "version_id" ), 
				odbc_result( $result, "filename_prefix" ),
				odbc_result( $result, "hl7_version" )); 

	printf("<td width=\"25%%\"><h3>%s</h3>\n</td>\n", 
				odbc_result( $result, "description" )); 
	printf("<td width=\"30%%\"><h4>%s</h4></td>\n", 
				odbc_result( $result, "status" )); 
	printf("<td align=\"center\" valign=\"center\" width=\"15%%\"><a href=%s/%s><img src=\"images/document.gif\" border=0 alt=\"Standard-Document\" ></a></td>\n", 
				odbc_result( $result, "HtmlPath" ),
				odbc_result( $result, "HtmlFile" )); 
	printf("<td align=\"center\" valign=\"center\" width=\"20%%\">released %s</td>\n", 
				date("d.m.Y", strtotime(odbc_result( $result, "date_release" )))); 


//	printf("<td>%s</td>\n", 
//				odbc_result( $result, $i )); 

	print "</tr>\n"; 
} 
print "</table>\n"; 

print "<hr>"; 

print "<p><b>The database currently maintains $Outer versions!</b></p>"; 

odbc_free_result($result);

//retrieve general infomration
$SQL = "SELECT line from HL7VersionComments where version_id=0 order by seq_no";

$result = odbc_exec($linkID, $SQL);
while( odbc_fetch_row( $result ))
{ 
	print odbc_result( $result, 1) . " "; 
}
odbc_free_result($result);

odbc_close($linkID);

PrintFooter();

?>
