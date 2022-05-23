<?php

include("db.php");

print "<!-- hl7_version.php -->";

$header1 = "HL7 - versionspecific Content ";
$header2 = "Contents";
$header3 = "";

$nButtonParameter = 0;
$sButtonParameter = "";
$sButtonType = "version";

include("hl7_vinfo.php");
include("hl7_header.php");
include("hl7_buttons.php");
include("hl7_footer.php");

// -- main part of program --
PrintHeader();

$SQL = "SELECT * from HL7VersionComments where version_id =" . $_GET["vVersion"] . " order by seq_no";

$result = odbc_exec($linkID, $SQL);

while( odbc_fetch_row( $result ))
{ 
	print odbc_result( $result, 3 ) . " "; 
} 

PrintFooter();

?>
