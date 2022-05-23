<?php

$nButtonParameter = 0;
if (isset($_GET["vTable"]))
{
	$sTable = $_GET["vTable"];
}
else
{
	$sTable = "";
}

$sButtonType = "table";
$sButtonParameter = "";
if ($sTable != "")
{
	$sButtonParameter = "&vTable=" . $sTable;
}
$ghl7_version="";
//$header1 = "HL7 Comprehensive Database ";

//open database
include("db.php");

$bEven = TRUE;

if ($sTable == "")
{
	//no table selected => display list of all

	$header2 = "List of Tables";
	$header3 = "";

	include("hl7_vinfo.php");
	include("hl7_header.php");
	include("hl7_buttons.php");

	print "<H2 align=center>";
	print "Overview about all Tables";
	print "</H2>\n";

	print "<p>\n"; 
	print "<table class=\"info\">\n"; 

//--------
	// Build Column Headers 
	$SQL = "SELECT * from HL7Versions where display=true and base_standard=0 order by sort";
	$result = odbc_exec($linkID, $SQL);

	print "<p>\n"; 
	print "<table class=\"info\">\n<tr class=\"info\">\n"; 
	// print heading information: versions
	print "<tr class=\"info\">"; 
	print "<th class=\"info\">"; 
	print "Table"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "Description\n"; 
	print "</th>\n"; 
	//...

	$i=0;
	while( odbc_fetch_row( $result ))
	{
		$i = $i + 1;
		printf("<th class=\"info\">%s</th>\n", odbc_result( $result, "hl7_version")); 
		$arrVersion[$i] = odbc_result( $result, "version_id");
	} 
	print "</tr>\n"; 
	odbc_free_result($result);	
	
	// Build Column Headers 
	$SQL = "SELECT HL7Tables.table_id, HL7Tables.version_id, HL7Versions.hl7_version, HL7Tables.description_as_pub, HL7Tables.contains_other, HL7Tables.contains_unknown, HL7Tables.deleted_values, HL7Tables.new_values FROM HL7Versions INNER JOIN HL7Tables ON HL7Versions.version_id = HL7Tables.version_id where table_id > 0 and HL7Versions.base_standard=0 order by table_id, HL7Versions.sort";
	
	$result = odbc_exec($linkID, $SQL);
	$Fields = odbc_num_fields($result); 
	
//----------------
	$Outer=0;
	$Tables=0;
	$nLastTable = 0;
	$i=0;
	$nNextOID=1;
	$nLastOID=0;
	$bFirst=true;
	
	// Table Body 
	while( odbc_fetch_row( $result ))
	{ 
		$Outer++;
		$i = $i + 1;
		$nLastVersion = odbc_result( $result, "version_id" );
		if ($nLastTable <> odbc_result( $result, "table_id" ))
		{
			//next table = row
			$bFirst=true;
			$Tables=$Tables+1;
			$nLastVersion=0;
			$sAktOID = "..18." . $nNextOID;
			$nAktVersion=1;
			$nNextOID = $nNextOID + 1;
			print "</tr>\n"; 
			$i=1;
			if ($bEven == FALSE)
			{
				$bEven = TRUE;
				print "<tr class=\"info-even\">"; 
			}
			else
			{
				$bEven = FALSE;
				print "<tr class=\"info-odd\">"; 
			}
			print "<td valign=\"center\">"; 
			printf( "<a href=\"hl7_table_cmp.php?vTable=%s\">%04d</a><br>\n", odbc_result( $result, "table_id" ), odbc_result( $result, "table_id" )); 
			print "</td>\n"; 
			print "<td>"; 
			printf( "%s<br>\n", odbc_result( $result, "description_as_pub" )); 
			print "</td>\n"; 
		}
		$nLastTable = odbc_result( $result, "table_id" );

		while ($arrVersion[$i] <> odbc_result( $result, "version_id" ))
		{
		//skip a column
			$i=$i+1;
			print "<td></td>\n"; 
		}

		print "<td valign=\"center\">"; 
		//print odbc_result( $result, "version_id" ) . "<br>\n"; 
		//print odbc_result( $result, "hl7_version" ) . "<br>\n"; 
		if (odbc_result( $result, "contains_other" )== true) {print "other<br>\n";}; 
		if (odbc_result( $result, "contains_unknown" )== true) {print "unknown<br>\n";}; 
		if (odbc_result( $result, "deleted_values" )== true) {print "deleted values<br>\n";	}; 
		if (odbc_result( $result, "new_values" )== true) {print "new values<br>\n";}; 
		if ($bFirst==false) {
			if (odbc_result( $result, "deleted_values" )== true) {
				//definitely a new codesystem
				$sAktOID = "..18." . $nNextOID;
				$nAktVersion=1;
				$nNextOID = $nNextOID + 1;
			} else {
				if (odbc_result( $result, "new_values" )== true) {
					if (odbc_result( $result, "contains_other" )== true || odbc_result( $result, "contains_unknown" )== true) {
						//a new codesystem
						$sAktOID = "..18." . $nNextOID;
						$nAktVersion=1;
						$nNextOID = $nNextOID + 1;
					} else {
						if (odbc_result( $result, "new_values" )== true) {
							$nAktVersion = $nAktVersion + 1;
						}
					}
				}
			}
		}
		print "<b>" . $sAktOID . " - v" . $nAktVersion . "</b>\n"; 
		print "</td>\n"; 
		$bFirst=false;
	} 
	print "</tr>\n"; 
	print "</table>\n"; 
	$nCount = $nNextOID-1;
	print "<p><b> The database currently maintains $Tables table(s) with $Outer variants using $nCount codesystems across all versions!</b></p>"; 

	odbc_free_result($result);
}
else
{
	//just this version
	//specific table selected

	$header2 = "Table Overview: " . sprintf ("%04d", $_GET["vTable"]);
	$header3 = "";

//	$sHtml = odbc_result( $result, "anchor" );
	$sChapter = "";

	include("hl7_vinfo.php");
	include("hl7_header.php");
	include("hl7_buttons.php");

	print "<H2 align=center>";
	print $header2;
	print "</H2>\n";
	print "<H3 align=center>";
	print $header3;
	print "</H3>\n";

//--------
	// Build Column Headers 

	print "<p>\n"; 
	print "For a specific table this view does not provide any information!\n"; 
	print "</p>\n"; 



//===========================

}

odbc_close($linkID);

include("hl7_footer.php");
?>

</body>
</html>
