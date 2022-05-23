<?php

include("hl7_header.php");
include("hl7_buttons.php");
include("hl7_footer.php");
include("hl7_vinfo.php");

$nButtonParameter = 0;
$sButtonType = "datastruct";
$sButtonParameter = "";

if (isset($_GET["vStruct"]))
{
	$sStruct = $_GET["vStruct"];
}
else
{
	$sStruct = "";
}

if ($sStruct != "")
{
	$sButtonParameter = "&vStruct=" . $sStruct;
}
$header1 = "HL7 Comprehensive Database ";

//open database
include("db.php");


$bEven = TRUE;

if ($sStruct == "")
{
	//no data struct selected => display list of all

	$header2 = "Data Structure List";

	PrintHeader();

	print "<H2 align=center>";
	print $header2;
	print "</H2>\n";

	$SQL = "SELECT data_structure,description,interpretation,data_type_code,elementary,date,anchor,section from HL7DataStructures where version_id =" . $_GET["vVersion"] . " order by data_structure;";

	$result = odbc_exec($linkID, $SQL);

	print "<div class=\"tabelle\" align=\"center\">\n";
	print "<p>\n"; 
	print "</p>\n"; 

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "<table class=\"info\">\n"; 

	// Build Column Headers 
	print "<tr class=\"info\">"; 
	print "<th class=\"info\">"; 
	print "Data Structure\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Description\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Interpretation\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Data Type\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Composite\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Sec. Ref.\n"; 
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
		printf("<a href=hl7_datastruct.php?vVersion=%s&vStruct=%s>\n", $_GET["vVersion"], odbc_result($result,1)); 
		printf("%s\n", odbc_result( $result, 1 )); 
		printf("</a>\n"); 
		print "</td>\n"; 
		print "<td class=\"info\" valign=\"center\">"; 
		$sText = odbc_result( $result, 2 );
		if ($sText == "")
			print "<br>\n";
		else
			printf("%s\n", $sText); 
		print "</td>\n"; 
		print "<td class=\"info\" valign=\"center\">"; 
		$sText = odbc_result( $result, 3 );
		if ($sText == "")
			print "<br>\n";
		else
			printf("%s\n", $sText); 
		print "</td>\n"; 
		print "<td class=\"info\">"; 
		printf("<a href=hl7_datatype.php?vVersion=%s&vType=%s>\n", $_GET["vVersion"], odbc_result($result,4)); 
		printf("%s\n", odbc_result( $result, 4 )); 
		printf("</a>\n"); 
		print "</td>\n"; 
		print "<td class=\"info\" valign=\"center\">"; 
		if (odbc_result( $result, 5 ) == 1)
			print "<br>\n";
		else
			print "yes\n"; 
		print "</td>\n"; 
		
		print "<td class=\"info\">"; 
		$sText = odbc_result( $result, "section" );
		if ($sText == "")
			$sText = "Std.";
		if (odbc_result( $result, "anchor" ) == "")
			$sOut = $sText;
		else
			$sOut = "<a href=" . $gHtmlPath. "/" . odbc_result($result,"anchor") . ">" . $sText . "</a>\n"; 
		if ($sOut == "Std.")
			$sOut = "<br>\n";
		print $sOut;
		print "</td>\n"; 
		
		print "</tr>\n"; 
	} 
	print "</table>\n"; 

	print "<p><b> The database currently maintains $Outer data structures for this version!</b></p>"; 

	print "</div>\n"; 

	odbc_free_result($result);
}
else
{
	//just this data struct
	
	$SQL = "SELECT * from HL7DataStructures where version_id = " . $_GET["vVersion"] . " and data_structure = '" . $sStruct . "'";

	$result = odbc_exec($linkID, $SQL);
	if (odbc_fetch_row( $result ))
	{
		$header2 = "Data Structure " . $sStruct;
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

	print "<h2>Chapter in Official Document: <a href=\"" . $sHtml . "\">" . $sChapter . "</a></H2>\n";


	$SQL = "SELECT HL7DataStructureComponents.data_structure, HL7DataStructureComponents.seq_no, HL7DataStructureComponents.comp_no, HL7Components.description, HL7Components.interpretation, HL7DataStructureComponents.min_length, HL7DataStructureComponents.max_length, HL7DataStructureComponents.conf_length, HL7DataStructureComponents.req_opt, 
	HL7Components.comp_no as comp_no2, HL7DataStructureComponents.table_id as table_id1, HL7Components.table_id as table_id2, HL7Components.data_type_code, HL7Components.data_structure as data_structure2, HL7Components.section, HL7Components.last_change
FROM HL7Components INNER JOIN HL7DataStructureComponents ON (HL7Components.version_id = HL7DataStructureComponents.version_id) AND (HL7Components.comp_no = HL7DataStructureComponents.comp_no) 
where HL7DataStructureComponents.version_id = " . $_GET["vVersion"] . " and HL7DataStructureComponents.data_structure = '" . $sStruct . "' order by seq_no";

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "<div class=\"tabelle\" align=\"center\">\n";
	print "<p>\n"; 
	print "</p>\n"; 
	print "<table class=\"info\">\n<tr class=\"info\">"; 

	// Build Column Headers 
	for ($i=1; $i <= $Fields; $i++){ 
		switch (odbc_field_name($result,$i))
		{
		case "data_structure":
		case "comp_no2":
		case "data_structure2":
		case "last_change":
			$sText = "";
			break;
		case "seq_no":
			$sText = "Seq.";
			break;
		case "comp_no":
			$sText = "Comp.";
			break;
		case "table_id1":
			$sText = "Table (this structure)";
			break;
		case "table_id2":
			$sText = "Table (this comp.)";
			break;
		case "min_length":
			$sText = "min";
			break;
		case "max_length":
			$sText = "max";
			break;
		case "conf_length":
			$sText = "C.LEN";
			break;
		case "interpretation":
			$sText = "Description/ <br>German Interpretation";
			break;
		case "section":
			$sText = "Sec. Ref.";
			break;
		case "data_type_code":
			$sText = "Data Type";
			break;
		case "data_structure":
			$sText = "Data Structure";
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
//print odbc_field_name($result,$i) . " ";
			switch (odbc_field_name($result,$i))
			{
			case "data_structure":
			case "comp_no2":
			case "data_structure2":
				//hide
				break;
			case "table_id1":
			case "table_id2":
				if (odbc_result($result,$i) != 0)
				{
					print "<td class=\"info\">"; 
					printf("<a href=hl7_table.php?vVersion=%s&vTable=%s>\n", $_GET["vVersion"], odbc_result($result,$i)); 
					printf("%04d\n", odbc_result( $result, $i )); 
					printf("</a>\n"); 
				}
				else
				{
					print "<td class=\"info\"><br></td>\n";
				}
				break;
			case "interpretation":
				$sText = odbc_result( $result, "description" ) . "<br>".odbc_result( $result, "interpretation" );
				if ($sText == "")
					$sText = "<br>";
				printf("<td class=\"info\">%s</td>\n", 
					$sText); 
				break;
			case "data_type_code":
				print "<td class=\"info\">"; 
				printf("<a href=hl7_datatype.php?vVersion=%s&vType=%s>\n", $_GET["vVersion"], odbc_result($result,"data_type_code")); 
				printf("%s\n", odbc_result( $result, "data_type_code" )); 
				printf("</a>\n"); 
				break;
			case "data_structure":
				print "<td class=\"info\">"; 
				printf("<a href=hl7_datastruct.php?vVersion=%s&vStruct=%s>\n", $_GET["vVersion"], odbc_result($result,"data_structure")); 
				printf("%s\n", odbc_result( $result, "data_structure" )); 
				printf("</a>\n"); 
				break;
			case "last_change":
//				printf("<td class=\"info\">%s</td>\n", 
//					date("d.m.Y", strtotime(odbc_result( $result, $i )))); 
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

	print "</div>\n"; 

	print "<p>\n"; 
	print "</p>\n"; 
	
//======================
	print "<div class=\"tabelle\" align=\"center\">\n";
	print "<p>\n"; 
	print "</p>\n"; 
	print "<H2>List all Data Elements using this Data Structure</H2>\n";
	
	$SQL = "SELECT data_item, description, interpretation, section, anchor FROM HL7DataElements where version_id = " . $_GET["vVersion"] . " and data_structure = '" . $sStruct . "'";

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 

	$bEven = TRUE;

	print "<p>\n"; 
	print "</p>\n"; 
	print "<table class=\"info\">\n<tr>"; 

	// Build Column Headers 
	for ($i=1; $i <= $Fields; $i++){ 
		switch ($i)
		{
		case 4:
			$sText = "";
			break;
		case 1:
			$sText = "Item";
			break;
		case 2:
			$sText = "Description";
			break;
		case 3:
			$sText = "German Interpretation";
			break;
		case 5:
			$sText = "Sec. Ref.";
			break;
		default:
			$sText = odbc_field_name( $result,$i); 
		}
		if ($sText != "")
			printf("<th class=\"info\">%s</th>\n", $sText); 
	} 
	print "</tr>\n"; 
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
			case 5:
//			case 2:
				//hide
				break;
			case 1:
				print "<td class=\"info\">"; 
				printf("<a href=hl7_dataelem.php?vVersion=%s&vData=%s>\n", $_GET["vVersion"], odbc_result($result,1)); 
				printf("%05d\n", odbc_result( $result, 1 )); 
				printf("</a>\n"); 
				print "</td>\n"; 

				break;
			case 4:
				print "<td class=\"info\">"; 
				$sText = odbc_result( $result, "section" );
				if ($sText == "")
					$sText = "Std.";
				if (odbc_result( $result, "anchor" ) == "")
					$sOut = $sText;
				else
					$sOut = "<a href=" . $gPath2GeneratedFiles . "\\" . $gHtmlPath. "/" . odbc_result($result,"anchor") . ">" . $sText . "</a>\n"; 
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

	print "</div>\n"; 

}

odbc_close($linkID);

PrintFooter();

?>

