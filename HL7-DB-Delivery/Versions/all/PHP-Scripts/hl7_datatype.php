<?php

include("hl7_vinfo.php");
include("hl7_header.php");
include("hl7_buttons.php");
include("hl7_footer.php");

$nButtonParameter = 0;
$sButtonType = "datatype";
$sButtonParameter = "";

if (isset($_GET["vType"]))
{
	$sType = $_GET["vType"];
}
else
{
	$sType = "";
}

if ($sType != "")
{
	$sButtonParameter = "&vType=" . $sType;
}
$header1 = "HL7 Comprehensive Database ";

//open database
include("db.php");


$bEven = TRUE;

if ($sType == "")
{
	//no event selected => display list of all

	PrintHeader();

	print "<H2 align=center>";
	print "List of all Data Types";
	print "</H2>\n";

	$SQL = "SELECT data_type_code,description,length,anchor from HL7DataTypes where version_id =" . $_GET["vVersion"] . " order by data_type_code;";

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "<div class=\"tabelle\" align=\"center\">\n";
	print "<p>\n"; 
	print "</p>\n"; 
	print "<table class=\"info\">\n"; 

	// Build Column Headers 
	print "<tr class=\"info\">"; 
	print "<th class=\"info\">\n"; 
	print "Data Type\n"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "Description\n"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "Length\n"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "Chapter\n"; 
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
			print "<tr class=\"info-even\">\n"; 
		}
		else
		{
			$bEven = FALSE;
			print "<tr class=\"info-odd\">\n"; 
		}
		print "<td>"; 
		printf("<a href=hl7_datatype.php?vVersion=%s&vType=%s>\n", $_GET["vVersion"], odbc_result($result,1)); 
		printf("%s\n", odbc_result( $result, 1 )); 
		printf("</a>\n"); 
		print "</td>"; 
		print "<td valign=\"center\">"; 
		printf("%s\n", odbc_result( $result, 2 )); 
		print "</td>"; 
		print "<td>"; 
		$sText = odbc_result( $result, 3 );
		if ($sText == "0") $sText = "<br>";
		if ($sText == "") $sText = "<br>";
		printf("%s\n", $sText); 
		print "</td>"; 
		print "<td>"; 
		printf("<a href=%s/%s>\n",  $gPath2GeneratedFiles . "\\" .  $gHtmlPath, odbc_result($result,4)); 

		print "Std.\n"; 
		printf("</a>\n"); 
		print "</td>"; 
		print "</tr>\n"; 
	} 
	print "</table>\n"; 
	print "<p><b> The database currently maintains $Outer data types for this version!</b></p>"; 
	print "</div>\n"; 

	odbc_free_result($result);
}
else
{
	//just this version
	//no event selected => display list of all

	$SQL = "SELECT * from HL7DataTypes where version_id = " . $_GET["vVersion"] . " and data_type_code = '" . $sType . "'";

	$result = odbc_exec($linkID, $SQL);
	if (odbc_fetch_row( $result ))
	{
		$header2 = "Data Type " . $sType;
		$header3 = odbc_result( $result, "description" );
	}
	$sHtml = odbc_result( $result, "anchor" );
//	$sChapter = odbc_result( $result, "section" );
	odbc_free_result($result);

	PrintHeader();

	print "<H2 align=center>";
	print $header2;
	print "</H2>\n";
	print "<H3 align=center>";
	print $header3;
	print "</H3>\n";

	print "<h2>Chapter in Official Document: <a href=\"" . $sHtml . "\">Std. Document</a></H2>\n";
//	print "<h2>Chapter in Official Document: " . $sChapter . "</H2>\n";


	$SQL = "SELECT data_type_code,data_structure,version_id,description,interpretation,section,anchor FROM HL7DataStructures " .
		"WHERE data_type_code='"  . $sType . "' AND version_id=" . $_GET["vVersion"] . " ORDER BY data_structure";

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<div class=\"tabelle\" align=\"center\">\n";
	print "<p>\n"; 
	print "</p>\n"; 
	print "<p>\n"; 
	print "<table class=\"info\">\n<tr>\n"; 

	// Build Column Headers 
	for ($i=1; $i <= $Fields; $i++){ 
		switch ($i)
		{
		case 1:
		case 3:
		case 7:
			$sText = "";
			break;
		case 2:
			$sText = "Data Structure";
			break;
		case 4:
			$sText = "Description";
			break;
		case 5:
			$sText = "German Interpretation";
			break;
		case 6:
			$sText = "Sec. Ref.";
			break;
		default:
			$sText = odbc_field_name( $result,$i); 
		}
		if ($sText != "")
			printf("<th bgcolor='silver'>%s</th>\n", $sText); 
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
			case 1:
			case 3:
			case 7:
				//hide
				break;
			case 6:
				print "<td><a href=" . $gPath2GeneratedFiles . "\\" . $gHtmlPath . "\\" . odbc_result($result,"anchor") . ">" . odbc_result($result,"section") . "</a></td>\n"; 

				break;
			default:
				$sText = odbc_result( $result, $i );
				if ($sText == "")
					$sText = "<br>";
				printf("<td>%s</td>\n", 
					$sText); 
				break;
			}
		}
		print "</tr>\n"; 
	} 
	print "</table>\n"; 
	print "</div>\n"; 

	odbc_free_result($result);
}

odbc_close($linkID);

PrintFooter();

?>
