<?php

include("hl7_vinfo.php");
include("hl7_header.php");
include("hl7_buttons.php");
include("hl7_footer.php");
include("hl7_get_option.php");

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

	PrintHeader();

	print "<H2 align=center>";
	print "List of all Tables";
	print "</H2>\n";

	$SQL = "SELECT HL7Tables.table_id, HL7Tables.description_as_pub, HL7Tables.interpretation, HL7TableTypes.description, HL7Tables.section, HL7Tables.anchor FROM HL7TableTypes INNER JOIN Hl7Tables ON HL7TableTypes.table_type = HL7Tables.table_type where version_id =" . $_GET["vVersion"] . " and table_id > 0 order by table_id";

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "<table class=\"info\">\n"; 

	// Build Column Headers 
	print "<tr class=\"info\">"; 
	print "<th class=\"info\">"; 
	print "Table"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "Description\n"; 
	print "</th>\n";
	print "<th class=\"info\">"; 
	print "German Interpretation\n"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "Typ\n"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "Sec. Ref.\n"; 
	print "</th>\n"; 
	print "</tr>\n"; 

	$Outer=0;
	// Table Body 
	while( odbc_fetch_row( $result ))
	{ 
		$Outer++;
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
		print "<td>"; 
		printf("<a href=hl7_table_cmp.php?vVersion=%s&vTable=%s>\n", $_GET[vVersion], odbc_result($result,1)); 
		printf("%04d\n", odbc_result( $result, 1 )); 
		printf("</a>\n"); 
		print "</td>"; 
		print "<td valign=\"center\">"; 
		printf("%s\n", odbc_result( $result, 2 )); 
		print "</td>\n"; 
		print "<td>"; 
		printf("%s\n", odbc_result( $result, 3 )); 
		print "</td>\n"; 
		print "<td>"; 
		printf("%s\n", odbc_result( $result, 4 )); 
		print "</td>\n"; 
		print "<td>"; 
		printf("<a href=\"" .  $gPath2GeneratedFiles . "/" . $gHtmlPath. "/%s\">\n",odbc_result($result,6)); 
		printf("%s\n", odbc_result( $result, 5 )); 
		printf("</a>\n"); 
		print "</td>\n"; 
		print "</tr>\n"; 
	} 
	print "</table>\n"; 
	print "<p><b> The database currently maintains $Outer table(s) for this version!</b></p>"; 

	odbc_free_result($result);
}
else
{
	//just this version
	//specific table selected

	$header2 = "Table Comparison: " . sprintf ("%04d", $_GET["vTable"]);
	$header3 = "";

//	$sHtml = odbc_result( $result, "anchor" );
	$sChapter = "";

	PrintHeader();

	
	print "<H2 align=center>";
	print $header2;
	print "</H2>\n";
	print "<H3 align=center>";
	print $header3;
	print "</H3>\n";

	$sFieldName = getOption(35);
	$sFieldComment = getOption(36);
	
//--------
	// Build Column Headers 
	
	//fetch table type
	$SQL = "SELECT * from HL7TableTypes order by table_type";
	$result = odbc_exec($linkID, $SQL);
	$i=0;
	while( odbc_fetch_row( $result ))
	{
		$i = $i + 1;
//		$arrTableType[$i] = odbc_result( $result, "description");
		$arrTableType[odbc_result( $result, "table_type")] = odbc_result( $result, "description");
		} 
	$nMaxTT = $i;
	odbc_free_result($result);
	
	//fetch Version
	$SQL = "SELECT * from HL7Versions where display=true and compare_table=true order by sort";
	$result = odbc_exec($linkID, $SQL);

	print "<p>\n"; 
	print "<table class=\"info\">\n<tr class=\"info\">\n"; 
	// print heading information: versions
	$i=0;
	while( odbc_fetch_row( $result ))
	{
		$i = $i + 1;
		$arrVersion[$i] = odbc_result( $result, "version_id");
		printf("<th class=\"info\"><a href=hl7_table.php?vVersion=%s&vTable=%s>%s</a></th>\n", odbc_result( $result, "version_id"), $sTable, odbc_result( $result, "hl7_version")); 
	} 
	$nMax = $i;
	print "</tr>\n"; 
	odbc_free_result($result);
	
//------------	
	// print other heading information
	$SQL = "SELECT HL7Versions.sort, HL7Tables.version_id, left(HL7Tables.description_as_pub,255) as desc2, HL7Tables.interpretation, HL7Tables.contains_other, HL7Tables.contains_unknown, HL7Versions.compare_table, HL7Tables.v2codetable, HL7Tables.table_type
FROM HL7Versions INNER JOIN HL7Tables ON HL7Versions.version_id = HL7Tables.version_id
WHERE (((HL7Tables.table_id)=" . $sTable . ") AND ((HL7Versions.compare_table)=True))
ORDER BY HL7Versions.sort;";

	$result = odbc_exec($linkID, $SQL);
	$i=0;
	//set color of 2nd header line
	print "<tr bgcolor=\"#aaaadd\">\n"; 
	while( odbc_fetch_row( $result ))
	{ 
		$i = $i + 1;
		$nLastVersion = odbc_result( $result, "version_id" );
		while ($arrVersion[$i] <> odbc_result( $result, "version_id" ))
		{
			//skip a column
			$i=$i+1;
			print "<td></td>\n"; 
		}

		print "<td class=\"info\">";
		print "<b>" . odbc_result( $result, "desc2" ) . "</b>";
//		print "<br>" . odbc_result( $result, "interpretation" );
		print "<br>" . $arrTableType[odbc_result( $result, "table_type" )];
		if (odbc_result( $result, "contains_other" ) == "1") {print "<br>contains \"other\";";};
		if (odbc_result( $result, "contains_unknown" ) == "1") {print "<br>contains \"unknown\";";};
		if (odbc_result( $result, "v2codetable" ) != "") {print "<br>" . odbc_result( $result, "v2codetable" ) . ";" ;};
		print "</td>\n";
	} 

	//print empty cell (fill line)
	while ($i < $nMax)
	{
		$i=$i+1;
		print "<td class=\"info\"></td>\n"; 
	}

	print "</tr>\n"; 
	
	odbc_free_result($result);
//------------	
	// Table Body: all values

	//the query has to return the first 255 character from a memo field only -> works!
	$SQL = "SELECT HL7Versions.sort, HL7TableValues.version_id, HL7TableValues.table_value,
	HL7TableValues." . $sFieldName . ", HL7TableValues.interpretation, left(
	HL7TableValues." . $sFieldComment . ",255) as " . $sFieldComment . "2 
FROM HL7Versions INNER JOIN HL7TableValues ON HL7Versions.version_id = HL7TableValues.version_id
WHERE (((HL7Versions.compare_table)=True) AND ((HL7TableValues.table_id)=" . $sTable . ") AND ((HL7TableValues.modification) <> 'D'))
ORDER BY HL7TableValues.table_value, HL7Versions.sort;";

	$result = odbc_exec($linkID, $SQL);
	//odbc_longreadlen( $result, 1000000);

	$Outer=0; 
	$nLastSort = 0;
	$bEven = FALSE;
	$i=0;
	$sLastValue = "";
	$sLastDescription = "";
	print "<tr class=\"info-odd\">\n"; 
	$sFieldComment = $sFieldComment . "2";
	
	while( odbc_fetch_row( $result ))
	{ 
		$i = $i + 1;
		if ($nLastSort >= odbc_result( $result, "sort" ) or $sLastValue <> odbc_result( $result, "table_value" ))
		{
			//start new row
			$sMark = "";
			$sLastDescription = "";
			
			//print empty cell (fill line)
			while ($i <= $nMax && $arrVersion[$i] <> "")
			{
				$i=$i+1;
				print "<td></td>\n"; 
			}
			
			$Outer++; 
			$i=1;
			if ($bEven == FALSE)
			{
				$bEven = TRUE;
				print "</tr>\n"; 
				print "<tr class=\"info-even\">\n"; 
			}
			else
			{
				$bEven = FALSE;
				print "</tr>\n"; 
				print "<tr class=\"info-odd\">\n"; 
			}
		}

		if ($sLastDescription <> "" && $sLastDescription <> odbc_result($result, $sFieldName))
		{
			//text has changed
			$sMark = "bgcolor='#ffaaaa'";
		}
		else
		{
			$sMark = "";
		}

		$nLastSort = odbc_result( $result, "sort" );
		$sLastValue = odbc_result( $result, "table_value" );
		$sLastDescription = odbc_result( $result, $sFieldName );
		while ($arrVersion[$i] <> odbc_result( $result, "version_id" ))
		{
			//skip a column
			$i=$i+1;
			print "<td></td>\n"; 
		}

		
		$sText = odbc_result( $result, "table_value" );
		$sText = str_replace("&", "&amp;", $sText);
		$sText = str_replace("<", "&lt;", $sText);
		$sText = str_replace(">", "&gt;", $sText);
		
		print "<td class=\"info\" valign=top " . $sMark . ">";
		print "<b>" . $sText . "</b><br>";

		$sText = odbc_result( $result, $sFieldName );
		$sText = str_replace("&", "&amp;", $sText);
		$sText = str_replace("<", "&lt;", $sText);
		$sText = str_replace(">", "&gt;", $sText);
		print $sText ;
		
//		print "<br>" . odbc_result( $result, "interpretation" );

		if (odbc_result( $result, $sFieldComment ) <> "")
		{
			print "<br>[" . odbc_result( $result, $sFieldComment ) . "]";
		}
		print "</td>\n";
	} 
	
	//print empty cell (fill line)
	while ($i < $nMax)
	{
		$i=$i+1;
		print "<td></td>\n"; 
	}


	print "</tr>\n"; 
	print "</table>\n"; 
	odbc_free_result($result);


	//list of all references

//======================
	

//===========================

}

odbc_close($linkID);

PrintFooter();

?>
