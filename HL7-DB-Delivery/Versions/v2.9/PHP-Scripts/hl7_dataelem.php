<?php

include("hl7_vinfo.php");
include("hl7_header.php");
include("hl7_buttons.php");
include("hl7_footer.php");

$nButtonParameter = 0;
$sButtonType = "dataelem";
$sButtonParameter = "";

if (isset($_GET["vData"]))
{
	$sData = $_GET["vData"];
}
else
{
	$sData = "";
}
if ($sData != "")
{
	$sButtonParameter = "&vData=" . $sData;
}
$header1 = "HL7 Comprehensive Database ";

//open database
include("db.php");

$bEven = TRUE;

if ($sData == "")
{
	//no data element selected => display list of all

	$header2 = "Data Element List";
	$header3 = "";

	PrintHeader();

	$SQL = "SELECT * from HL7DataElements where version_id =" . $_GET["vVersion"] . " order by data_item;";

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "<table class=\"info\">\n"; 

	// Build Column Headers 
	print "<tr class=\"info\">"; 
	print "<th class=\"info\">"; 
	print "Data Element\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Description\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Data Structure\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Length\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Conf. Length\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Table\n"; 
	print "</th>";
	print "<th class=\"info\">"; 
	print "Sec. Ref.\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Last Change\n"; 
	print "</th>"; 
	print "</tr>\n"; 

	$Outer=0;
	// Table Body 
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
		print "<td class=\"info\">"; 
		printf("<a href=hl7_dataelem.php?vVersion=%s&vData=%s>\n", $_GET["vVersion"], odbc_result($result,1)); 
		printf("%05d\n", odbc_result( $result, 1 )); 
		printf("</a>\n"); 
		print "</td>"; 
		print "<td class=\"info\" valign=\"center\">"; 
		printf("%s<br>\n", odbc_result( $result, "description" )); 
		printf("%s\n", odbc_result( $result, "interpretation" )); 
		print "</td>"; 
		print "<td class=\"info\">"; 
		printf("<a href=hl7_datastruct.php?vVersion=%s&vStruct=%s>\n", $_GET["vVersion"], odbc_result($result,"data_structure")); 
		printf("%s\n", odbc_result( $result, "data_structure" )); 
		printf("</a>\n"); 
		print "</td>"; 
		print "<td class=\"info\">"; 
		printf("[%s..%s]\n", odbc_result($result,"min_length"), odbc_result($result,"max_length")); 
		print "</td>"; 
		print "<td class=\"info\">"; 
		printf("%s\n", odbc_result($result,"conf_length")); 
		print "</td>"; 
		print "<td class=\"info\">"; 
		if (odbc_result( $result, "table_id" ) == "" || odbc_result( $result, "table_id" ) == "0")
		{
			print "<br>";
		}
		else
		{
			printf("<a href=hl7_table.php?vVersion=%s&vTable=%s>\n", $_GET["vVersion"], odbc_result($result,"table_id")); 
			printf("%04d\n", odbc_result( $result, "table_id" )); 
		}
		printf("</a>\n"); 
		print "</td>"; 
		print "<td class=\"info\">"; 
		$sText = odbc_result( $result, "section" );
		if ($sText == "")
			$sText = "Std.";
		if (odbc_result( $result, "anchor" ) == "")
			$sOut = $sText;
		else
			$sOut = "<a href=" . $gPath2GeneratedFiles . $gPathDelimiter . $gHtmlPath . $gPathDelimiter . odbc_result($result,"anchor") . ">" . $sText . "</a>\n"; 
		if ($sOut == "Std.")
			$sOut = "<br>\n";
		print $sOut;
		print "</td>\n"; 
		printf("<td class=\"info\">%s</td>\n", 
			date("d.m.Y", strtotime(odbc_result( $result, "date" )))); 
		print "</tr>\n"; 
	} 
	print "</table>\n"; 
	print "<p><b> The database currently maintains $Outer data elements for this version!</b></p>"; 

	odbc_free_result($result);
}
else
{
	//just this data element

	//query information for header
	$SQL = "SELECT * from HL7DataElements where version_id = " . $_GET["vVersion"] . " and data_item = " . $sData;

	$result = odbc_exec($linkID, $SQL);
	if (odbc_fetch_row( $result ))
	{
		$header2 = "Data Element " . sprintf("%05d", $sData);
		$header3 = odbc_result( $result, "description" ) . "<br>" . odbc_result( $result, "interpretation" );
	}
	$sHtml = odbc_result( $result, "anchor" );
	$sChapter = odbc_result( $result, "section" );
	odbc_free_result($result);

	PrintHeader();

	print "<H2 align=center>";
	print $header2;
	print "</H2>\n";
	print "<H3 align=center>";
	print $header3;
	print "</H3>\n";

	print "<h2>see Chapter: <a href=\"" . $sHtml . "\">" . $sChapter . "</a></H2>\n";

	//requery the main information
	$SQL = "SELECT * from HL7DataElements where version_id = " . $_GET["vVersion"] . " and data_item = " . $sData;
	$result = odbc_exec($linkID, $SQL);
	
	print "<table class=\"info\">\n";
	print "<tr class=\"info\">\n";
	print "<td class=\"info\">Description:</td><td class=\"info\"> " . odbc_result( $result, "description" ) . "</td>\n";
	print "</tr>\n";
	print "<tr>\n";
	print "<td class=\"info\">Interpretation:</td><td class=\"info\"> " . odbc_result( $result, "interpretation" ) . "</td>\n";
	print "</tr>\n";
	print "<tr class=\"info\">\n";
	print "<td class=\"info\">Data Structure:</td><td class=\"info\"> " . odbc_result( $result, "data_structure" ) . "</td>\n";
	print "</tr>\n";
	print "<tr class=\"info\">\n";
	print "<td class=\"info\">Length:</td><td class=\"info\"> " . odbc_result( $result, "min_length" ) . " - " . odbc_result( $result, "min_length" ) . "</td>\n";
	print "</tr>\n";
	if (odbc_result( $result, "table_id" ) != "0")
	{
		print "<tr class=\"info\">\n";
		printf("<td class=\"info\">Table:</td><td class=\"info\">%04d</td>\n", odbc_result( $result, "table_id" ));
		print "</tr>\n";
	}
	print "</table>\n";

	odbc_free_result($result);

	$SQL = "SELECT HL7SegmentDataElements.seg_code, HL7SegmentDataElements.seq_no, HL7Segments.description, HL7Segments.interpretation, HL7Segments.anchor, HL7Segments.section 
FROM HL7Segments INNER JOIN HL7SegmentDataElements ON (HL7Segments.version_id = HL7SegmentDataElements.version_id) AND (HL7Segments.seg_code = HL7SegmentDataElements.seg_code) 
where HL7SegmentDataElements.version_id = " . $_GET["vVersion"] . " and HL7SegmentDataElements.data_item = " . $sData . " order by HL7SegmentDataElements.seg_code";
	
	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "<h2>Used in the following Segments (Overview)</H2>\n";
	print "<table class=\"info\">\n<tr class=\"info\">"; 

	// Build Column Headers 
	for ($i=1; $i <= $Fields; $i++){ 
		switch ($i)
		{
		case 6:
			$sText ="";
			break;
		case 1:
			$sText = "Segment";
			break;
		case 2:
			$sText = "Seq#";
			break;
		case 3:
			$sText = "Description";
			break;
		case 4:
			$sText = "German Interpretation";
			break;
		case 5:
			$sText = "Std. Chp.";
			break;
		default:
			$sText = odbc_field_name( $result,$i); 
		}
		if ($sText != "")
			printf("<th class=\"info\">%s</th>\n", $sText); 
	} 
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
		for($i=1; $i <= $Fields; $i++)
		{ 
			switch ($i)
			{
			case 6:
//			case 2:
//				//hide
				break;
			case 1:
				print "<td class=\"info\">"; 
				printf("<a href=hl7_segment.php?vVersion=%s&vSegment=%s>\n", $_GET["vVersion"], odbc_result($result,1)); 
				printf("%s\n", odbc_result( $result, 1 )); 
				printf("</a>\n"); 
				print "</td>\n"; 
				break;
			case 5:
				print "<td class=\"info\">"; 
				$sText = odbc_result( $result, "section" );
				if ($sText == "")
					$sText = "Std.";
				if (odbc_result( $result, "anchor" ) == "")
					$sOut = $sText;
				else
					$sOut = "<a href=" . $gPath2GeneratedFiles . $gPathDelimiter . $gHtmlPath . $gPathDelimiter . odbc_result($result,"anchor") . ">" . $sText . "</a>\n"; 
				if ($sOut == "Std.")
					$sOut = "<br>\n";
				print $sOut;
				print "</td>\n"; 
				break;
			default:
				$sText = odbc_result( $result, $i );
				if ($sText == "")
					$sText = "<br>";
				printf("<td class=\"info\">%s</td>\n", 
					$sText); 
				break;
			}
		}
		print "</tr>\n"; 
	} 
	print "</table>\n"; 

	odbc_free_result($result);

	//find sections using the same statement again
	
	$SQL = "SELECT HL7SegmentDataElements.seg_code, HL7SegmentDataElements.seq_no, HL7Segments.description, HL7Segments.interpretation, HL7Segments.anchor, HL7Segments.section 
FROM HL7Segments INNER JOIN HL7SegmentDataElements ON (HL7Segments.version_id = HL7SegmentDataElements.version_id) AND (HL7Segments.seg_code = HL7SegmentDataElements.seg_code) 
where HL7SegmentDataElements.version_id = " . $_GET["vVersion"] . " and HL7SegmentDataElements.data_item = " . $sData . " order by HL7SegmentDataElements.seg_code";
	
	$result = odbc_exec($linkID, $SQL);

	print "<p>\n"; 
	print "<h2>Used in the following Segments (including Description)</H2>\n";

	// Table Body 
	while( odbc_fetch_row( $result ))
	{ 
		$sSection = odbc_result( $result, "section" );
		include("hl7_sections.php");

		$sSection = odbc_result( $result, "section" ) . "." . odbc_result($result,"seq_no");
		include("hl7_sections.php");
	} 
	odbc_free_result($result);

}

odbc_close($linkID);

PrintFooter();
?>
