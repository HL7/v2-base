<?php

include("hl7_vinfo.php");
include("hl7_header.php");
include("hl7_buttons.php");
include("hl7_footer.php");

$nButtonParameter = 0;
if (isset($_GET["vType"]))
{
	$sMsgType = $_GET["vType"];
}
else
{
	$sMsgType = "";
}


$sButtonType = "msgtype";
if ($sMsgType != "")
{
	$sButtonParameter = "&vType=" . $sMsgType;
}
else
{
	$sButtonParameter = "";
}
$header1 = "HL7 Comprehensive Database ";

//open database
include("db.php");

$bEven = TRUE;

if ($sMsgType == "")
{
	//no event selected => display list of all

	PrintHeader();

	print "<H2 align=center>";
	print "List of all Message Types";
	print "</H2>\n";

	$SQL = "SELECT message_type, description, section FROM HL7MessageTypes where version_id =" . $_GET["vVersion"] . " order by message_type;";

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "<table class=\"info\">\n<tr>"; 

	// Build Column Headers 
	print "<tr class=\"info\">"; 
	print "<th class=\"info\">"; 
	print "Message Type\n"; 
	print "</th>"; 
	print "<th class=\"info\">"; 
	print "Description\n"; 
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
		printf("<a href=hl7_msgtype.php?vVersion=%s&vType=%s>\n", $_GET["vVersion"], odbc_result($result,1)); 
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
		$sText = odbc_result( $result, "section" );
		if ($sText == "")
			$sText = "<br>";
		print $sText;
		print "</td>\n"; 

		print "</tr>\n"; 
	} 
	print "</table>\n"; 
	print "<p><b> The database currently maintains $Outer message types for this version!</b></p>"; 

	odbc_free_result($result);
}
else
{
	//just this version
	//no event selected => display list of all

	$SQL = "SELECT message_type, description, section FROM HL7MessageTypes where version_id =" . $_GET["vVersion"] . " and message_type = '". $sMsgType ."';";

	$result = odbc_exec($linkID, $SQL);
	if (odbc_fetch_row( $result ))
	{
		$header2 = "Message Type " . $sMsgType;
		$header3 = odbc_result( $result, "description" );
	}


//	$sHtml = odbc_result( $result, "anchor" );
	$sChapter = odbc_result( $result, "section" );
	odbc_free_result($result);

	PrintHeader();

	print "<P>";
	print "<H2 align=center>";
	print $header2;
	print "</H2>\n";
	print "<H3 align=center>";
	print $header3;
	print "</H3>\n";

//	if ($sOut != "")
//		print "<h2>see Chapter: " . $sChapter . "</H2>\n";
		

	$SQL = "SELECT HL7EventMessageTypes.event_code, HL7EventMessageTypes.version_id, HL7EventMessageTypes.message_typ_snd, HL7EventMessageTypes.message_typ_return, HL7Events.description, HL7Events.interpretation, HL7Events.section, HL7Events.anchor
FROM HL7Events INNER JOIN HL7EventMessageTypes ON (HL7Events.version_id = HL7EventMessageTypes.version_id) AND (HL7Events.event_code = HL7EventMessageTypes.event_code) where HL7EventMessageTypes.version_id = " . $_GET["vVersion"] . " and (message_typ_snd = '" . $sMsgType . "' or message_typ_return = '" . $sMsgType . "') order by HL7EventMessageTypes.event_code;";

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 

	// Build Column Headers 
	print "<p>\n"; 
	print "<table class=\"info\">\n"; 
	print "<tr class=\"info\">"; 
	print "<th class=\"info\">"; 
	print "Event\n"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "Description\n"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "German Interpretation\n"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "Sec. Ref.\n"; 
	print "</th>\n"; 
	print "</tr>\n"; 

	// Table Body 
	$Outer=0;
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

		printf("<a href=hl7_event.php?vVersion=%s&vEvent=%s>", $_GET["vVersion"], odbc_result($result,"event_code")); 
		print  odbc_result( $result, "event_code" ); 
		printf("</a>"); 
		print "</td>"; 

		print "<td class=\"info\">"; 
		print  odbc_result( $result, "description" ); 
		print "</td>\n"; 
		print "<td class=\"info\">"; 
		print  odbc_result( $result, "interpretation" ); 
		print "</td>\n"; 

		print "<td class=\"info\">"; 
		printf("<a href=%s/%s>\n",  $gPath2GeneratedFiles . $gPathDelimiter . $gHtmlPath, $gPathDelimiter . odbc_result($result,"anchor")); 
		print  odbc_result( $result, "section" ); 
		printf("</a>\n"); 
		print "</td>"; 
		print "</tr>\n"; 


	} 
	print "</table>\n"; 
	print "<p><b> The database currently maintains $Outer events for this message type!</b></p>"; 

	odbc_free_result($result);
}

odbc_close($linkID);

PrintFooter();

?>
