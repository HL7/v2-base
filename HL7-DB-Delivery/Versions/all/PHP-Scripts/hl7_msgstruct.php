<?php

include("hl7_vinfo.php");
include("hl7_header.php");
include("hl7_buttons.php");
include("hl7_footer.php");

$nButtonParameter = 0;
$sButtonParameter = "";
$sButtonType = "msgstruct";

if (isset($_GET["vStruct"]))
{
	$sMsgStruct = $_GET["vStruct"];
}
else
{
	$sMsgStruct = "";
}


if ($sMsgStruct != "")
{
	$sButtonParameter = "&vStruct=" . $sMsgStruct;
}
$header1 = "HL7 Comprehensive Database ";

//open database
include("db.php");


$bEven = TRUE;

if ($sMsgStruct == "")
{
	//no msg struct selected => display list of all

	PrintHeader();

	print "<H2 align=center>";
	print "List of all Message Structures";
	print "</H2>\n";

	$SQL = "SELECT message_structure, description, example_event, example_msg_type, section, anchor
FROM HL7MsgStructIDs where version_id =" . $_GET["vVersion"] . " and message_structure <> '?' order by message_structure;";


	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "<table class=\"info\">\n"; 

	// Build Column Headers 
	print "<tr class=\"info\">"; 
	print "<th class=\"info\">"; 
	print "Message Structure\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Description\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Example Event\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Example Message Type\n"; 
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
			print "<tr class=\"info-even\">"; 
		}
		else
		{
			$bEven = FALSE;
			print "<tr class=\"info-odd\">"; 
		}

		print "<td class=\"info\">"; 
		printf("<a href=hl7_msgstruct.php?vVersion=%s&vStruct=%s>\n", $_GET["vVersion"], odbc_result($result,1)); 
		printf("%s\n", odbc_result( $result, 1 )); 
		printf("</a>\n"); 
		print "</td>"; 
		$sText = odbc_result( $result, 2 );
		if ($sText == "")
			$sText = "<br>";
		print "<td class=\"info\" valign=\"center\">"; 
		printf("%s\n", $sText); 
		print "</td>"; 
		print "<td class=\"info\">"; 
		if (odbc_result($result,3) <> "")
		{
			printf("<a href=hl7_event.php?vVersion=%s&vEvent=%s>\n", $_GET["vVersion"], odbc_result($result,3)); 
			printf("%s\n", odbc_result( $result, 3 )); 
			printf("</a>\n"); 
		}
		else
			printf("<br>\n"); 
		print "</td>"; 
		print "<td class=\"info\">"; 
		if (odbc_result($result,4) <> "")
		{
			printf("<a href=hl7_msgtype.php?vVersion=%s&vTypeType=%s>\n", $_GET["vVersion"], odbc_result($result,4)); 
			printf("%s\n", odbc_result( $result, 4 )); 
			printf("</a>\n"); 
		}
		else
			printf("<br>\n"); 
		print "</td>"; 


		print "<td class=\"info\">"; 
		$sText = odbc_result( $result, "section" );
		if ($sText == "")
			$sText = "Std.";
		if (odbc_result( $result, "anchor" ) == "")
			$sOut = $sText;
		else
			$sOut = "<a href=" . $gPath2GeneratedFiles . "/" . $gHtmlPath . "/" . odbc_result($result,"anchor") . ">" . $sText . "</a>\n"; 
		if ($sOut == "Std.")
			$sOut = "<br>\n";
		print $sOut;
		print "</td>\n"; 

		print "</tr>\n"; 
	} 
	print "</table>\n"; 
	print "<p><b> The database currently maintains $Outer message structures for this version!</b></p>"; 

	odbc_free_result($result);
}
else
{
	//just this version
	//msg struct selected

	PrintHeader();
	
	$SQL = "SELECT message_structure, description, example_event, example_msg_type, section, anchor FROM HL7MsgStructIDs where version_id =" . $_GET["vVersion"] . " and message_structure = '". $sMsgStruct."';";

	$result = odbc_exec($linkID, $SQL);
	if (odbc_fetch_row( $result ))
	{
		$header2 = "Message Structure " . $sMsgStruct;
		$header3 = odbc_result( $result, "description" );
	}

	$sText = odbc_result( $result, "section" );
	if ($sText == "")
		$sText = "Std.";
	if (odbc_result( $result, "anchor" ) == "")
		$sOut = $sText;
	else
		$sOut = "<a href=" . $gPath2GeneratedFiles . "/" . $gHtmlPath. "/" . odbc_result($result,"anchor") . ">" . $sText . "</a>\n"; 
	if ($sOut == "Std.")
		$sOut = "";

	$sHtml = odbc_result( $result, "anchor" );
	$sChapter = odbc_result( $result, "section" );
	odbc_free_result($result);

	print "<H2 align=center>";
	print $header2;
	print "</H2>\n";
	print "<H3 align=center>";
	print $header3;
	print "</H3>\n";
	
	if ($sOut != "")
		print "<h2>see Chapter: " . $sOut . "</H2>\n";
		
//create message structure
	$SQL = "SELECT seq_no, seg_code, groupname, repetitional, optional from HL7MsgStructIDSegments where version_id = " . $_GET["vVersion"] . " and message_structure = '" . $sMsgStruct . "' order by seq_no;";

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 

	$sMax = "                                    ";
	print "<pre>\n"; 
	$sIndent = "";
	// Table Body 
	while( odbc_fetch_row( $result ))
	{ 
		switch (odbc_result( $result, "seg_code" ))
		{
		case "[":
		case "{":
		case "<":
		case "[{":
			$sPrint = false;
			break;
		case "}]":
		case "]":
		case "}":
		case ">":
			$sPrint = false;
			//reduce indentation
			$sIndent = substr($sIndent, 0, strlen($sIndent)-3);
			break;
		case "|":
			$sPrint = false;
			break;
		default:
			$sPrint = true;
			break;
		}

		$sLine = $sIndent;

		if (odbc_result( $result, "repetitional" ) == 1)
			$sLine = $sLine . "{  ";
		if (odbc_result( $result, "optional" ) == 1)
			$sLine = $sLine . "[  ";
		
		if ($sPrint == true)
		{
			$sLine = $sLine . "<a href=hl7_segment.php?vVersion=" . $_GET["vVersion"] . "&vSegment=" . odbc_result($result,"seg_code") . ">"; 
			$sLine = $sLine .  odbc_result( $result, "seg_code" ); 
			$sLine = $sLine . "</a>"; 
		}
		else
		{
			$sLine = $sLine . odbc_result( $result, "seg_code" ); 
		}
		if (odbc_result( $result, "groupname" ) <> "")
		{
			$sLine = $sLine . substr($sMax,strlen($sLine)) . odbc_result( $result, "groupname" ). " ";
		}

		if (odbc_result( $result, "optional" ) == 1)
			$sLine = $sLine . "  ]";
		if (odbc_result( $result, "repetitional" ) == 1)
			$sLine = $sLine . "  }";

		print $sLine . "\n"; 
		switch (odbc_result( $result, "seg_code" ))
		{
		case "[{":
		case "[":
		case "{":
		case "<":
			//increase indentation
			$sIndent = $sIndent . "   ";
			break;
		}

	} 
	print "</pre>\n"; 

	odbc_free_result($result);

}

odbc_close($linkID);

PrintFooter();

?>

