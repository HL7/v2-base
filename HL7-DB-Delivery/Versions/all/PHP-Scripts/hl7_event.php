<?php

include("hl7_vinfo.php");
include("hl7_header.php");
include("hl7_buttons.php");
include("hl7_footer.php");

$nButtonParameter = 0;
if (isset($_GET["vEvent"]))
{
	$sEvent = $_GET["vEvent"];
}
else
{
	$sEvent = "";
}
$sButtonType = "event";
$sButtonParameter = "";
if ($sEvent != "")
{
	$sButtonParameter = "&vEvent=" . $sEvent;
}
$ghl7Version="";
$header1 = "HL7 Comprehensive Database ";
$bEven = TRUE;

//open database
include("db.php");

if ($sEvent == "")
{
	//no event selected => display list of all

	$header2 = "Eventlist";
	$header3 = "";

	PrintHeader();

	print "<H2 align=center>";
	print "List of all Events";
	print "</H2>\n";
//	print "<H3>";
//	print $header3;
//	print "</H3>\n";

	$SQL = "SELECT event_code,description,interpretation,anchor,section from HL7Events where version_id =" . $_GET["vVersion"] . " order by event_code";

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "</p>\n"; 
	print "<table class=\"info\">\n"; 

	// Build Column Headers 
	print "<tr class=\"info\">\n"; 
	print "<th class=\"info\">"; 
	print "Event\n"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "Description"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "German Interpretation"; 
	print "</th>\n"; 
	print "<th class=\"info\">"; 
	print "Sec. Ref."; 
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
		print "<td >"; 
		printf("<a href=\"hl7_event.php?vVersion=%s&vEvent=%s\">", $_GET["vVersion"], odbc_result($result,1)); 
		printf("%s", odbc_result( $result, 1 )); 
		printf("</a>"); 
		print "</td>\n"; 
		print "<td>"; 
		printf("%s", odbc_result( $result, 2 )); 
		print "</td>\n"; 
		print "<td>"; 
		printf("%s", odbc_result( $result, 3 )); 
		print "</td>\n"; 
		print "<td >"; 
		printf("<a href=%s/%s>",  $gPath2GeneratedFiles . "/" .  $gHtmlPath, odbc_result($result,4)); 
		printf("%s", odbc_result( $result, 5 )); 
		printf("</a>"); 
		print "</td>\n"; 
		print "</tr>\n"; 
	} 
	print "</table>\n"; 
	print "<p>\n<b> The database currently maintains $Outer event(s)!</b>\n</p>\n\n"; 

	odbc_free_result($result);
}
else
{
	//just this version
	//no event selected => display list of all

	$SQL = "SELECT * from HL7Events where version_id = " . $_GET["vVersion"] . " and event_code = '" . $sEvent . "'";

	$result = odbc_exec($linkID, $SQL);
	if (odbc_fetch_row( $result ))
	{
		$header2 = "Event " . $sEvent;
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

	print "<h2>Official Document: <a href=\"" . $gPath2GeneratedFiles . "/" . $gHtmlPath . "/" .$sHtml . "\">" . $sChapter . "</a></H2>\n";

	$SQL = "SELECT seq_no, message_typ_snd, message_typ_return, message_structure_snd, message_structure_return FROM HL7EventMessageTypes where version_id = " . $_GET["vVersion"] . " and event_code = '" . $sEvent . "'";
	$Outer=0;

	$result = odbc_exec($linkID, $SQL);
	while( odbc_fetch_row( $result ))
	{ 
		print "<hr width=60%>\n";
		print "<h2>Messages used by</h2>\n";
		$Outer++;
		print "<table>\n";
		print "<tr>\n";
		print "<td>\n";
		print "</td>\n";
		print "<td>\n";
		print "<h3>the sending application/facility:</h3>\n";
		print "<td>\n";
		print "</td>\n";
		print "<td>\n";
		print "<h3>the receiving application/facility:</h3>\n";
		print "</td>\n";
		print "</tr>\n";

		print "<tr>\n";
		print "<td align=right>\n";
		print "Message type:"; 
		print "</td>\n";
		print "<td align=center>\n";
		printf("<a href=hl7_msgtype.php?vVersion=%s&vMsgTyp=%s>\n", $_GET["vVersion"], odbc_result($result,"message_typ_snd")); 
		printf("%s\n", odbc_result( $result, "message_typ_snd" )); 
		printf("</a>\n"); 
		print "<td>=&gt;</td>\n";
		print "<td align=center>\n";
		printf("<a href=hl7_msgtype.php?vVersion=%s&vMsgTyp=%s>\n", $_GET["vVersion"], odbc_result($result,"message_typ_return")); 
		printf("%s\n", odbc_result( $result, "message_typ_return" )); 
		printf("</a>\n"); 
		print "</td>\n";
		print "</tr>\n";

		print "<tr>\n";
		print "<td align=right>\n";
		print "Message structure:"; 
		print "</td>\n";
		print "<td align=center>\n";
		printf("<a href=hl7_msgstruct.php?vVersion=%s&vStruct=%s>\n", $_GET["vVersion"], odbc_result($result,"message_structure_snd")); 
		printf("%s\n", odbc_result( $result, "message_structure_snd" )); 
		print "<td>=&gt;</td>\n";
		print "<td align=center>\n";
		printf("<a href=hl7_msgstruct.php?vVersion=%s&vStruct=%s>\n", $_GET["vVersion"], odbc_result($result,"message_structure_return")); 
		printf("%s\n", odbc_result( $result, "message_structure_return" )); 
		printf("</a>\n"); 
		print "</td>\n";
		print "</tr>\n";
		print "</table>\n";
		
		print "<br>\n"; 

//=======================
//start message structure

		print "<h3>Message Structures specific for this Event</h3>\n";
		$SQL1 = "SELECT seq_no, seg_code, groupname, repetitional, optional from HL7EventMessageTypeSegments where version_id = " . $_GET["vVersion"] . " and message_type = '" . odbc_result($result,"message_typ_snd") . "' and event_code = '" . $sEvent . "' order by seq_no";
		$SQL2 = "SELECT seq_no, seg_code, groupname, repetitional, optional from HL7EventMessageTypeSegments where version_id = " . $_GET["vVersion"] . " and message_type = '" . odbc_result($result,"message_typ_return") . "' and event_code = '" . $sEvent . "' order by seq_no";

		include("hl7_message.php");

		print "</tr>\n";

// end message structure
//======================

	} 

	odbc_free_result($result);

}

odbc_close($linkID);

PrintFooter();

?>
