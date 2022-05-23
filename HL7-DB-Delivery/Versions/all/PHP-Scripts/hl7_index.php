<?php

include("hl7_vinfo.php");
include("hl7_header.php");
include("hl7_buttons.php");
include("hl7_footer.php");

$nButtonParameter = 0;
$sButtonType = "index";
$sButtonParameter = "";

if (isset($_GET["vChar"]))
{
	$sChar = $_GET["vChar"];
}
else
{
	$sChar = "";
}

if ($sChar != "")
{
	$sButtonParameter = "&vChar=" . $sChar;
}
$header1 = "HL7 Comprehensive Database ";

//open database
include("db.php");

if ($sChar == "")
{
	//no char selected => display list of all

	$header2 = "Index";
	$header3 = "";

	PrintHeader();

	$SQL = "SELECT * from GeneratedDataIndex where version_id =" . $_GET["vVersion"] . " order by language,character,text";

	$result = odbc_exec($linkID, $SQL);
	$last_char = "";
	$last_lang = "";

	$Fields = odbc_num_fields($result); 
	print "<h2>\n"; 
	print $header2;
	print "</h2>\n"; 

	// Table Body 
	while( odbc_fetch_row( $result ))
	{ 
		if ($last_lang <> odbc_result($result, "language"))
		{
			if ($last_lang <> "")
			{
				print "</table>\n"; 
			}
			$last_lang = odbc_result($result, "language");
		
			print "<table class=\"info\" width='100%'>\n<tr>"; 
		
			// Build Column Headers 
			print "<tr class=\"info\">"; 
			print "<th class=\"info\">"; 
			print "Character\n"; 
			print "</th>"; 
			print "<th class=\"info\">"; 
			print "Text\n"; 
			print "</th>"; 
			print "<th class=\"info\">"; 
			print "Hyperlink\n"; 
			print "</th>"; 
			print "<th class=\"info\">"; 
			print "Sec. Ref.\n"; 
			print "</th>"; 
			print "</tr>\n"; 
		}
		

		if ($last_char <> odbc_result ($result, "character"))
		{
			print "<tr class=\"info\">"; 
			print "<th class=\"info\">"; 
			$last_char = odbc_result ($result, "character");
			print "<H2>" . $last_char . "</H2></th><th class=\"info\"><br></th><th class=\"info\"><br></th><th class=\"info\"><br></th>\n</tr>\n<tr><td>\n";
			$bEven = FALSE;
		}
		else
		{
			if ($bEven == FALSE)
			{
				$bEven = TRUE;
				print "<tr class=\"segm-even\">\n"; 
			}
			else
			{
				$bEven = FALSE;
				print "<tr class=\"segm-odd\">\n"; 
			}

			print "<td class=\"info\">"; 
			print "<br>\n"; 
		}
		
		print "</td>"; 
		print "<td class=\"info\" valign=\"center\">"; 
		printf("%s\n", odbc_result( $result, "text" )); 
		print "</td>\n"; 
		print "<td class=\"info\">"; 
		switch (odbc_result($result,"type"))
		{
		case 5:
			//table
			printf("<a href=hl7_table.php?vVersion=%s&vTable=%s>%04s</a>\n", $_GET["vVersion"], odbc_result($result,"value"), odbc_result($result,"value")); 
			break;
		case 7:
			//data element
			printf("<a href=hl7_dataelem.php?vVersion=%s&vData=%s>%05s</a>\n", $_GET["vVersion"], odbc_result($result,"value"), odbc_result($result,"value")); 
			break;
		case 9:
			//Segment
			printf("<a href=hl7_segment.php?vVersion=%s&vSegment=%s>\n", $_GET["vVersion"], odbc_result($result,"value")); 
			print odbc_result($result,"value"); 
			print "</a>"; 
			break;
		case 11:
			//Event
			printf("<a href=hl7_event.php?vVersion=%s&vEvent=%s>\n", $_GET["vVersion"], odbc_result($result,"value")); 
			print odbc_result($result,"value"); 
			print "</a>"; 
			break;
		case 22:
			//message structure
			printf("<a href=hl7_msgstruct.php?vVersion=%s&vStruct=%s>\n", $_GET["vVersion"], odbc_result($result,"value")); 
			print odbc_result($result,"value"); 
			print "</a>"; 
			break;
		case 24:
			//Query
			printf("<a href=hl7_query.php?vVersion=%s&vQuery=%s>\n", $_GET["vVersion"], odbc_result($result,"value")); 
			print odbc_result($result,"value"); 
			print "</a>"; 
			break;
		case 26:
			//msgtype
			printf("<a href=hl7_msgtype.php?vVersion=%s&vTypeType=%s>\n", $_GET["vVersion"], odbc_result($result,"value")); 
			print odbc_result($result,"value"); 
			print "</a>"; 
			break;
		default:
			//unknown
			print odbc_result($result,"value"); 
			break;
		}
		print "</td>\n"; 
		printf("<td class=\"info\"><a href=\"%s\">%s</a></td>\n", 
				$gPath2GeneratedFiles . $gPathDelimiter . $gHtmlPath . $gPathDelimiter . odbc_result( $result, "anchor" ),
				odbc_result( $result, "section" )); 
		print "</tr>\n"; 
	} 
	print "</table>\n"; 

	odbc_free_result($result);
}
else
{
	//char selected => display list of all possible texts

	PrintHeader();
	
	$header2 = "Index";
	$header3 = "";

	include("hl7_vinfo.php");
	include("hl7_header.php");
	include("hl7_buttons.php");

	print "<h1>\n";
	print "Not yet implemented!!!!!\n";
	print "</h1>\n";
}

odbc_close($linkID);

PrintFooter();
?>

