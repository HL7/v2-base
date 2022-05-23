<?php

include("hl7_header.php");
include("hl7_buttons.php");
include("hl7_footer.php");
include("hl7_vinfo.php");

print "<!-- hl7_segment.php -->\n";

function getDelta ($linkID, $Segment, $No, $Field)
{
	$ret="";
	$value_old = "";
	$value_new = "";
	
	$SQLx = "SELECT version_id,value_old, value_new from GeneratedDataDifferences where version_id=" . $_GET["vVersion"] . " and element='" . $Segment . "' and lfd_nr=" . $No . " and diff_type='" . $Field . "';";

	$result2 = odbc_exec($linkID, $SQLx);

	if ($result2 == TRUE)
	{
		$value_old = odbc_result($result2, "value_old");
		$value_new = odbc_result($result2, "value_new");
		//get this value to distinguish between no value and a null value row!
		//(odbc_num_rows dows not work!)
		$version_id = odbc_result($result2, "version_id");
		
		if ($value_old <> $value_new or $version_id <> "")
		{
			$ret="BGCOLOR=\"#FFB090\"";
		}
	}
	odbc_free_result($result2);
	return $ret;
}

$sButtonType = "segment";
$nButtonParameter = 0;

if (isset($_GET["vSegment"]))
{
	$sSegment = $_GET["vSegment"];
	$sButtonParameter = "&vSegment=" . $sSegment;
}
else
{
	$sSegment = "";
	$sButtonParameter = "&vSegment=" . $sSegment;
}

if ($sSegment != "")
{
	//keine Zusatzaktion für Segmente
	//$sButtonParameter = "&vSegment=" . $sSegment;
}
$header1 = "HL7 Comprehensive Database ";

//open database
include("db.php");


$bEven = TRUE;

if ($sSegment == "")
{
	//no segment selected => display list of all

	$header2 = "Segmentlist";
	$header3 = "";

	PrintHeader();
	
	print "<H2 align=center>";
	print "List of all Segment-Definitions";
	print "</H2>\n\n";

	$SQL = "SELECT seg_code,description,interpretation,anchor,section from HL7Segments where version_id =" . $_GET["vVersion"] . " and visible=true order by seg_code";

	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "</p>\n"; 
	print "<div class=\"tabelle\" align=\"center\">\n";
	print "<table class=\"info\">\n"; 

	// Build Column Headers 
	print "<tr class=\"info\">\n"; 
	print "<th class=\"info\">"; 
	print "Segment"; 
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
			print "<tr class=\"segm-even\">\n"; 
		}
		else
		{
			$bEven = FALSE;
			print "<tr class=\"segm-odd\">\n"; 
		}
		print "<td class=segm>"; 
		printf("<a href=\"hl7_segment.php?vVersion=%s&vSegment=%s\">", $_GET["vVersion"], odbc_result($result,1)); 
		printf("%s", odbc_result( $result, 1 )); 
		printf("</a>"); 
		print "</td>\n"; 
		print "<td valign=\"center\" class=segm>"; 
		printf("%s", odbc_result( $result, 2 )); 
		print "</td>\n"; 
		print "<td valign=\"center\" class=segm>"; 
		printf("%s", odbc_result( $result, 3 )); 
		print "</td>\n"; 
		print "<td  class=segm>"; 
		printf("<a href=\"" . $gPath2GeneratedFiles . "/" . $gHtmlPath . "/%s\">", odbc_result($result,4)); 
		printf("%s", odbc_result( $result, 5 )); 
		printf("</a>"); 
		print "</td>\n"; 
		print "</tr>\n"; 
	} 
	print "</table>\n"; 
	print "</div>\n";
	print "<p>\n<b> The database currently maintains $Outer segment(s) for this version!</b>\n</p>\n\n"; 

	odbc_free_result($result);
}
else
{
	//just this version
	//segment selected => display details

	$SQL = "SELECT * from HL7Segments where version_id = " . $_GET["vVersion"] . " and seg_code = '" . $sSegment . "'";

	$result = odbc_exec($linkID, $SQL);
	if (odbc_fetch_row( $result ))
	{
		$header2 = "Segment " . $sSegment;
		$header3 = odbc_result( $result, "description" ) . "<br>" . odbc_result( $result, "interpretation" );
	}
	$sHtml = odbc_result( $result, "anchor" );
	$sChapter = odbc_result( $result, "section" );
	$sLastFieldRep = odbc_result( $result, "last_field_repeatable" );
	odbc_free_result($result);

	PrintHeader();

	print "<H2 align=center>";
	print $header2;
	print "</H2>\n";
	print "<H3 align=center>";
	print $header3;
	print "</H3>\n";

	print "<h2>See Chapter: <a href=\"" . $gPath2GeneratedFiles . "/" . $gHtmlPath. "/" . $sHtml . "\">" . $sChapter . "</a></H2>\n";

	$sSection = $sChapter;
	include("hl7_sections.php");


	$SQL = "SELECT HL7SegmentDataElements.seg_code, HL7SegmentDataElements.version_id, " . 
			"HL7SegmentDataElements.seq_no, " . 
			"HL7DataElements.description, HL7DataElements.interpretation, " .
			"HL7DataElements.min_length, " .
			"HL7DataElements.max_length, " .
			"HL7DataElements.conf_length, " .
			"HL7DataElements.table_id, HL7SegmentDataElements.req_opt, " .
			"HL7SegmentDataElements.repetitional, HL7SegmentDataElements.repetitions, " .
			"HL7SegmentDataElements.data_item, HL7DataElements.data_structure, " .
			"HL7SegmentDataElements.section, HL7SegmentDataElements.anchor " . 
			"FROM HL7DataElements INNER JOIN HL7SegmentDataElements ON (HL7DataElements.version_id = HL7SegmentDataElements.version_id) AND (HL7DataElements.data_item = HL7SegmentDataElements.data_item)" .
			"WHERE (((HL7SegmentDataElements.seg_code)= '" . $sSegment . "') AND ((HL7SegmentDataElements.version_id)=" . $_GET["vVersion"] . ")) ORDER BY HL7SegmentDataElements.seq_no;";


	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<p>\n"; 
	print "</p>\n"; 

	print "<div class=\"tabelle\" align=\"center\">\n";
	print "<table class=\"info\">\n<tr>\n"; 

	// Build Column Headers 
	for ($i=1; $i <= $Fields; $i++){ 
		switch (odbc_field_name($result,$i))
		{
		case "seg_code":
		case "version_id":
		case "repetitions":
		case "anchor":
			$sText = "";
			break;
		case "seq_no":
			$sText = "Seq";
			break;
		case "min_length":
			$sText = "";
			break;
		case "max_length":
			$sText = "Length";
			break;
		case "conf_length":
			$sText = "C.LEN";
			break;
		case "table_id":
			$sText = "Table";
			break;
		case "req_opt":
			$sText = "r/o/c";
			break;
		case "repetitional":
			$sText = "Rep#";
			break;
		case "description":
			$sText = "Description";
			break;
		case "interpretation":
			$sText = "German Interpretation";
			break;
		case "data_item":
			$sText = "Item";
			break;
		case "data_structure":
			$sText = "Data Structure";
			break;
		case "section":
			$sText = "Std. Chp.";
			break;
		default:
			$sText = odbc_field_name( $result,$i); 
		}
		if ($sText != "")
			printf("<th class=\"info\">%s</th>\n", $sText); 

	} 
	print "</tr>\n"; 

	// build Table Body with details
	$Outer=0; 
	while( odbc_fetch_row( $result ))
	{ 
		$Outer++; 
		$markieren=getDelta($linkID, odbc_result( $result, "seg_code" ), odbc_result( $result, "seq_no" ), "new_row");
		if ($markieren <> "")
		{
			printf("<tr %s>", $markieren); 
		}
		else
		{
			if ($bEven == FALSE)
			{
				$bEven = TRUE;
				print "<tr class=\"segm-even\">"; 
			}
			else
			{
				$bEven = FALSE;
				print "<tr class=\"segm-odd\">"; 
			}
		}
		$markieren="";

		for($i=1; $i <= $Fields; $i++)
		{ 
			switch (odbc_field_name($result, $i))
			{
			case "seg_code":
			case "version_id":
			case "repetitions":
			case "anchor":
			case "max_length":
				//hide
				break;
			case "table_id":
				$markieren=getDelta($linkID, odbc_result( $result, "seg_code" ), odbc_result( $result, "seq_no" ), "table_id");
				if (odbc_result( $result, "table_id" ) == 0)
				{
					printf("<td class=\"segm\"><br></td>\n"); 
				}
				else
				{
					printf("<td class=\"segm\" %s><a href=\"hl7_table.php?vVersion=%s&vTable=%s\">%04d</a></td>\n", 
						$markieren,
						odbc_result( $result, "version_id" ), 
						odbc_result( $result, "table_id" ),
						odbc_result( $result, "table_id" )); 
				}
				break;
			case "min_length":
				$markieren=getDelta($linkID, odbc_result( $result, "seg_code" ), odbc_result( $result, "seq_no" ), "min_length") or getDelta($linkID, odbc_result( $result, "seg_code" ), odbc_result( $result, "seq_no" ), "max_length");

				if ((odbc_result( $result, "min_length" ) == "") AND (odbc_result( $result, "max_length" ) == ""))
				{
					printf("<td class=\"segm\"><BR></td>\n"); 
				}
				else
				{
					printf("<td class=\"segm\" %s>%s..%s</td>\n", 
							$markieren,
							odbc_result( $result, "min_length" ),
							odbc_result( $result, "max_length" )); 
				}
				$markieren="";


				break;
			case "req_opt":
				$markieren=getDelta($linkID, odbc_result( $result, "seg_code" ), odbc_result( $result, "seq_no" ), "req_opt");
				$sText = odbc_result($result, "req_opt");
				if ($sText == "")
					$sText = "<br>";
				print "<td class=\"segm\" " . $markieren . ">" . $sText . "</td>\n"; 
				break;
			case "repetitional":
				$markieren=getDelta($linkID, odbc_result( $result, "seg_code" ), odbc_result( $result, "seq_no" ), "repetitional");
				$sText = odbc_result($result, "repetitional");
				if (odbc_result($result, "repetitions") != "" && odbc_result($result, "repetitions") != "0")
				{
					if ($markieren == "")
					{
						//execute only if not marked already
						$markieren=getDelta($linkID, odbc_result( $result, "seg_code" ), odbc_result( $result, "seq_no" ), "repetitions");
					}
					$sText = $sText . "/" . odbc_result($result, "repetitions");
				}
				if ($sText == "")
					$sText = "<br>";
				print "<td class=\"segm\" " . $markieren . ">" . $sText . "</td>\n"; 
				break;
			case "data_item":
				printf("<td class=\"segm\"><a href=\"hl7_dataelem.php?vVersion=%s&vData=%s\">%05d</a></td>\n", 
					odbc_result( $result, "version_id" ), 
					odbc_result( $result, "data_item" ),
					odbc_result( $result, "data_item" )); 
				break;
			case "data_structure":
				$markieren=getDelta($linkID, odbc_result( $result, "seg_code" ), odbc_result( $result, "seq_no" ), "data_structure");
				printf("<td class=\"segm\" %s><a href=\"hl7_datastruct.php?vVersion=%s&vStruct=%s\">%s</a></td>\n", 
					$markieren,
					odbc_result( $result, "version_id" ), 
					odbc_result( $result, "data_structure" ),
					odbc_result( $result, "data_structure" )); 
				break;
			case "section":  
				printf("<td class=\"segm\"><a href=\"%s\">%s</a></td>\n", 
					$gPath2GeneratedFiles . $gPathDelimiter . $gHtmlPath . $gPathDelimiter . odbc_result($result,"anchor"),
					odbc_result( $result, "section" )); 
				break;
			default:
				//identify whether changes have been occured
				switch (odbc_field_name($result, $i))
				{
				case "interpretation":
					$markieren=getDelta($linkID, odbc_result( $result, "seg_code" ), odbc_result( $result, "seq_no" ), "interpretation");
					break;
				case "description";
					$markieren=getDelta($linkID, odbc_result( $result, "seg_code" ), odbc_result( $result, "seq_no" ), "description");
					break;
				case "conf_length";
					$markieren=getDelta($linkID, odbc_result( $result, "seg_code" ), odbc_result( $result, "seq_no" ), "conf_length");
					break;
				}
				//hier fehlt bewusst das break!!!!!
			case "seq_no":
				if (odbc_result( $result, $i ) == "")
				{
					printf("<td class=\"segm\"><BR></td>\n"); 
				}
				else
				{
					printf("<td class=\"segm\" %s>%s</td>\n", 
							$markieren,
							odbc_result( $result, $i )); 
				}
				$markieren="";
				break;
			}
		}
		print "</tr>\n"; 
	} 
	if ($sLastFieldRep == 1)
	{
		//fill in symbolic row
		if ($bEven == FALSE)
		{
			$bEven = TRUE;
			print "<tr class=\"segm-even\">"; 
		}
		else
		{
			$bEven = FALSE;
			print "<tr class=\"segm-odd\">"; 
		}
		print "<td class=\"segm\">...\n</td>\n"; 
		print "</tr>\n"; 
	}
	print "</table>\n"; 
	print "</div>\n";

	odbc_free_result($result);

//===============
//list of all message structures using this segment

	print "<p>\n"; 
	print "</p>\n"; 
	print "<div class=\"tabelle\" align=\"center\">\n";
	print "<p>\n"; 
	print "</p>\n"; 
	print "<H2>List of all Message Structures using this segment</H2>\n";
	
	$SQL = "SELECT HL7MsgStructIDs.message_structure, HL7MsgStructIDs.version_id, HL7MsgStructIDs.description, "
	. "HL7MsgStructIDs.example_event, HL7MsgStructIDs.example_msg_type, HL7MsgStructIDs.action, HL7MsgStructIDs.section, "
	. "HL7MsgStructIDs.anchor, HL7MsgStructIDSegments.seg_code, HL7MsgStructIDSegments.version_id "
	. "FROM HL7MsgStructIDs INNER JOIN HL7MsgStructIDSegments ON (HL7MsgStructIDs.version_id = HL7MsgStructIDSegments.version_id) "
	. "AND (HL7MsgStructIDs.message_structure = HL7MsgStructIDSegments.message_structure) "
	. "WHERE (((HL7MsgStructIDSegments.seg_code)='" . $sSegment . "') AND ((HL7MsgStructIDSegments.version_id)=" . $_GET["vVersion"] . " ))";


	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 

	print "<table class=\"info\">\n<tr>\n"; 	
	
	// Build Column Headers 
	for ($i=1; $i <= $Fields; $i++){ 
		switch ($i)
		{
		case 1:
			$sText = "Message Structure";
			break;
		case 2: //version_id
		case 8: //anchor
		case 9: //seg_code
		case 10: //version_id
			$sText = "";
			break;
		case 3:
			$sText = "Description";
			break;
		case 4:
			$sText = "Example Event";
			break;
		case 5:
			$sText = "Example Msg Type";
			break;
		case 6:
			$sText = "Action";
			break;
		case 7:
			$sText = "Section";
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
	$bEven = TRUE;
	while( odbc_fetch_row( $result ))
	{ 
		$Outer++; 

		if ($bEven == FALSE)
		{
			$bEven = TRUE;
			print "<tr class=\"segm-even\">"; 
		}
		else
		{
			$bEven = FALSE;
			print "<tr class=\"segm-odd\">"; 
		}

		for($i=1; $i <= $Fields; $i++)
		{ 
			switch ($i)
			{
			case 1:
				printf("<td class=\"info\"><a href=hl7_msgstruct.php?vVersion=%s&vStruct=%s>%s</a></td>\n", 
					$_GET["vVersion"],
					odbc_result( $result, "message_structure" ),
					odbc_result( $result, "message_structure" ));
				break;
			case 2: //version_id
			case 8: //anchor
			case 9: //seg_code
			case 10: //version_id
				//hide
				break;
			case 7:
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
	print "</div>\n"; 

	odbc_free_result($result);

//===============
//list of all events using this segment directly

	print "<p>\n"; 
	print "</p>\n"; 
	print "<div class=\"tabelle\" align='center'>\n"; 
	print "<p>\n"; 
	print "</p>\n"; 
	print "<H2>List of all Events using this Segment directly</H2>\n";
	
//	$SQL = "SELECT DISTINCTROW HL7EventMessageTypes.event_code, HL7Events.description, HL7Events.interpretation, HL7Events.section, HL7Events.anchor
//FROM (HL7Events INNER JOIN HL7EventMessageTypes ON (HL7Events.version_id = HL7EventMessageTypes.version_id) AND (HL7Events.event_code = HL7EventMessageTypes.event_code)) 
//INNER JOIN HL7EventMessageTypeSegments ON (HL7Events.version_id = HL7EventMessageTypeSegments.version_id) AND (HL7Events.event_code = HL7EventMessageTypeSegments.event_code) 
//WHERE HL7EventMessageTypeSegments.version_id = " . $_GET["vVersion"] . " and seg_code = '" . $sSegment . "' order by HL7EventMessageTypes.event_code;";


$SQL="SELECT HL7Events.event_code, HL7Events.version_id, HL7EventMessageTypeSegments.message_type, "
. "HL7Events.description, HL7Events.interpretation, HL7Events.section, HL7Events.anchor, HL7EventMessageTypeSegments.seg_code, "
. "HL7EventMessageTypeSegments.version_id "
. "FROM HL7Events INNER JOIN HL7EventMessageTypeSegments ON (HL7Events.version_id = HL7EventMessageTypeSegments.version_id) "
. "AND (HL7Events.event_code = HL7EventMessageTypeSegments.event_code) "
. "WHERE (((HL7EventMessageTypeSegments.seg_code)= '" . $sSegment . "') "
. "AND ((HL7EventMessageTypeSegments.version_id)=" . $_GET["vVersion"] ."));";



	$result = odbc_exec($linkID, $SQL);

	$Fields = odbc_num_fields($result); 
	print "<table class=\"info\" >\n<tr>\n"; 

	// Build Column Headers 
	for ($i=1; $i <= $Fields; $i++){ 
		switch ($i)
		{
		case 1:
			$sText = "Event";
			break;
		case 2: //version_id
		case 7: //anchor
		case 8: //seg_code
		case 9: //version_id
			$sText = "";
			break;
		case 3:
			$sText = "Msg Type";
			break;
		case 4:
			$sText = "Description";
			break;
		case 5:
			$sText = "Interpretation";
			break;
		case 6:
			$sText = "Section";
			break;
		default:
			$sText = odbc_field_name( $result,$i); 
		}
		if ($sText != "")
			printf("<th class=\"info\">%s</th>\n", $sText); 
	} 
	// Table Body 
	$Outer=0; 
	$bEven = TRUE;
	while( odbc_fetch_row( $result ))
	{ 
		$Outer++; 
		if ($bEven == FALSE)
		{
			$bEven = TRUE;
			print "<tr class=\"segm-even\">"; 
		}
		else
		{
			$bEven = FALSE;
			print "<tr class=\"segm-odd\">"; 
		}

		for($i=1; $i <= $Fields; $i++)
		{ 
			switch ($i)
			{
			case 1:
				printf("<td class=\"info\"><a href=hl7_event.php?vVersion=%s&vEvent=%s>%s</a></td>\n", 
					$_GET["vVersion"],
					odbc_result( $result, "event_code" ),
					odbc_result( $result, "event_code" ));
				break;
			case 2: //version_id
			case 7: //anchor
			case 8: //seg_code
			case 9: //version_id
				//hide
				break;
			case 3:
				printf("<td class=\"info\"><a href=hl7_msgtype.php?vVersion=%s&vType=%s>%s</a></td>\n", 
					$_GET["vVersion"],
					odbc_result( $result, "message_type" ),
					odbc_result( $result, "message_type" ));
				break;
			case 6:
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

	print "</div>\n"; 

}

odbc_close($linkID);

PrintFooter();

?>
